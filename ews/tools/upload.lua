--
-- tools's file upload processor
--
-- usage:
--
--   local upload = require 'tools.upload'
--   upload.process('/opt/static/img', 'images')
--
local log    = require 'tools.log'
local fs     = require 'tools.fs'
local random = require 'tools.random'
local upload = require 'resty.upload'

local random_id = random.id
local dexists = fs.dexists
local insert, concat = table.insert, table.concat
local say, re_match = ngx.say, ngx.re.match
local io_open = io.open
local to_lower, str_gsub = string.lower, string.gsub
local pairs, type, traceback = pairs, type, debug.traceback

-- Valid file extensions
local valid = {}
valid.images = { tools=1, jpg=1, jpeg=1, gif=1, bmp=1 }
valid.docs   = { pdf=1, doc=1, docx=1, htm=1, html=1, md=1, txt=1, markdown=1 }

local API = {}


--- multipart/form-data parser 
--
-- Parse a header part of a 'multipart/form-data' to obtain 
-- a field name and/or a filename
--
-- example:
--   log(API:_parse_part('Content-Disposition: form-data; name="datafile"; filename="test.txt"'))
-- returns: 
--   datafile, test, txt
-- 
-- @param  header The header object of a multipart/form-data part obtained by upload:read()
-- @return name The name of a field name="xx" if found
-- @return filename The filename if found
-- @return ext The file extension if found
function API:_parse_part(header)
  if type(header) == 'table' then header = concat(header) end
  if type(header) ~= 'string' or header == '' then return end
  local file_re = [[ filename="([a-z0-9-_ \.]+)\.([a-z0-9-_]{2,4})"]]
  local field_re = [[ name="([a-z0-9-_\.]+)"]]
  local filename = re_match(header, file_re, 'i')
  local name = re_match(header, field_re, 'i')
  return name and name[1], filename and filename[1], filename and filename[2]
end


--- Validates a file extension
-- @param ext The file extension to validate: jpg .jpeg .txt
-- @param ext_type The file type: image, doc
-- @return 1 if file extension is valid, or false otherwise
function API:_is_valid_ext(ext, ext_type)
  local typ
  if type(ext) ~= 'string' or ext == '' then return false end
  if ext_type and (not valid[ext_type] and ext_type ~= 'any') then 
    return nil, log('invalid ext_type: ' .. ext_type)
  end
  if not ext_type then typ = 'any' end
  ext = to_lower(str_gsub(ext, '^%.', ''))
  if valid[ext_type] then
    return valid[ext_type] and valid[ext_type][ext]
  end
  -- any case
  for k, _ in pairs(valid) do
    if valid[k][ext] then return 1 end
  end
  return false
end


--- Process a file upload
--
-- Process multipart/form-data post using lua-resty-upload
-- parse headers to obtain filenames and fields and validate
-- file extensions to accept
--
-- @param path The path to save files to
-- @param filetype Hardcoded file extensions by groups: 'images' 'docs' 'any' 
-- @return table fields, files and invalids
function API:process(path, filetype)
  -- validate output path
  if not path or type(path) ~= 'string' then 
    return nil, log('Path missing, must be a string')
  end
  local exists, err = dexists(path)
  if not exists then
    return nil, log('Can\'t open path: "' .. path .. '" error was:', err)
  end

  -- validate filetype group
  if filetype and (not valid[filetype] and filetype ~= 'any') then 
    local valids = {}
    for k, _ in pairs(valid) do valids[#valids+1]=k end
    return nil, log('Invalid filetype: ' .. filetype ..
                    ' valids are: ' .. concat(valids, ', ') .. ', any')
  end
  if not filetype then filetype = 'any' end

  -- Instantiate new uploader object
  local chunk_size = 4096
  local form = upload:new(chunk_size)
  form:set_timeout(2000) -- 2 sec

  local fields = {}
  local files = {}
  local invalid = {}
  local next_field
  local file -- io handler

  while true do
    local typ, res, err = form:read()

    if not typ then
      return log(ngx.ERR, {reason='failed to read form:read(): ' .. tostring(err), traceback=traceback()})
    end

    -- read and parse a header section
    if typ == 'header' then
      local field, filename, ext = self:_parse_part(res)

      if field then 
        next_field = true
        local value = ''
        if filename and ext then value = filename .. '.' .. ext end
        fields[#fields+1] = {
          name = field,
          value = value
        }
      else 
        next_field = nil
      end

      -- create new file
      if filename and ext and self:_is_valid_ext(ext, filetype) then
        files[#files+1] = {
          filename = random_id(17),
          ext = ext
        }
        local newfile = files[#files].filename .. '.' .. ext
        file, err = io_open(path .. '/' .. newfile, 'w+')
        if not file then
          return log(ngx.ERR, {
            reason='failed to open file for write ' .. tostring(newfile) .. ': '.. tostring(err), 
            traceback=traceback()
          })
        end
      -- ignore invalid file type
      elseif filename and ext and not self:_is_valid_ext(ext, filetype) then
        invalid[#invalid+1] = {
          filename = filename,
          ext = ext
        }
      end

    -- read body content
    elseif typ == 'body' then
      if file then
        file:write(res)
      elseif next_field then
        fields[#fields].value = res
      end

    -- finish body
    elseif typ == 'part_end' then
      if file then
        file:close()
        file = nil
      end

    -- end
    elseif typ == 'eof' then
      break

    -- do nothing
    else
    end

  end
  return {
    fields  = fields,
    files   = files,
    invalid = invalid
  }
end

-----------------------------------------------------------
-- return API
-----------------------------------------------------------
local mt = { __index = API }
return setmetatable({}, mt)
