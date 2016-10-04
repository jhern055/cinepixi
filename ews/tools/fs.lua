-- Dependencies
local log = require 'tools.log'

-- locals, prevent global scope lookup, grouped by module
local ngx = ngx
local tonumber, tostring   = tonumber, tostring
local insert, concat, sort = table.insert, table.concat, table.sort
local pairs, ipairs, type  = pairs, ipairs, type
local error, pcall = error, pcall
local open, close, rename = io.open, io.close, os.rename

-- PNG's Filesystem Utilities
local _M = {}

--- Check if a file exists
-- @param filename Filename to check if exists 
function _M.fexists(filename)
  local f, err = pcall(open(filename, 'r'))
  if not f then
    return false, err
  else 
    close(f) 
    return true
  end
end

--- Check if a directory exists
-- Using os.rename's method for now
-- @param dirname Directory name to check if exists
function _M.dexists(dirname)
  local d, err = rename(dirname, dirname)
  if not d then 
    return false, err
  else
    return true
  end
end

--- Read a file a return their contents
-- @param filename 
-- @param mode open mode
function _M.read_file(filename, mode)
  local ok, res = pcall(function()
    local f = open(filename, mode or 'r')
    local content = f:read("*all")
    f:close()
    return content
  end)
  if not ok then
    return nil, log(ngx.ERR, 'Error reading file ' .. filename .. ' res: ', res)
  end
  return res
end

return _M
