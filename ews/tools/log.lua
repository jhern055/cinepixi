--
-- TOOLS's Application Logger
--

-- Dependencies
local cjson  = require 'cjson'
local config = require 'config'

-- Globals Mapping
local ngx, null = ngx, ngx.null
local concat, insert = table.concat, table.insert
local encode = cjson.encode
local traceback = debug.traceback
local tostring, tonumber = tostring, tonumber
local say, localtime, ngx_log = ngx.say, ngx.localtime, ngx.log
local type, open = type, io.open

-- Statics
local STDERR  = ngx.STDERR  -- 0
local EMERG   = ngx.EMERG   -- 1
local ALERT   = ngx.ALERT   -- 2
local CRIT    = ngx.CRIT    -- 3
local ERR     = ngx.ERR     -- 4
local WARN    = ngx.WARN    -- 5
local NOTICE  = ngx.NOTICE  -- 6
local INFO    = ngx.INFO    -- 7
local DEBUG   = ngx.DEBUG   -- 8
local JUSTSAY = 9           -- 9 default level, just do a ngx.say() + return msg string

-- Open logfile, one file-descriptor per nginx's worker
local logfile = open(config.log, 'a')
if not logfile then
  ngx_log(ERR, 'Error opening TOOLS log file:', config.log)
end

--- TOOLS main logger
--
-- Save json encoded log message to $config.log
-- Pass the message to ngx.log
-- Returns the message as string
-- 
-- @param level Log level 0-9 (0=STDERR .. 9=DEBUG)
-- @params multiple objects
-- @return string with all the messages converted and concatenated
local log = function(level, ...)
  local arguments = {...}
  if type(level) == 'number' and #arguments == 0 then
    say(level)
    return level
  end
  if type(level) ~= 'number' or ( type(level) == 'number' and tonumber(level) > 9 ) then 
    insert(arguments, 1, level)
    level = JUSTSAY
  end
  local msg = {
    level = level,
    time = localtime()
  }

  -- Check level and if we got an error table with {reason and traceback}
  if level >= STDERR and level < JUSTSAY then 
    if type(arguments[1]) == 'table' and arguments[1].traceback then
      msg.traceback = arguments[1].traceback
      if arguments[1].reason then
        msg.msg = arguments[1].reason
      end
    else
      msg.traceback = traceback()
    end
  end

  -- detect holes (nil) in arguments table (1,2,3,nil,nil,4)
  -- XXX: can't detect trailing nils in pure Lua 5.1 :(
  local tlen = #arguments
  local fi = 0
  for i = tlen+1,tlen+10 do
    fi = fi + 1
    if arguments[i] ~= nil then
      tlen = tlen + fi
    end
  end

  local items = {}
  for i = 1,tlen do
    local v = arguments[i]
    local typ = type(v)
    if       typ == 'number'   then items[#items+1] = v
      elseif typ == 'string'   then items[#items+1] = v
      elseif typ == 'table'    then 
        local ok, res = pcall(encode, v)
        if ok then
          items[#items+1] = res
        else
          items[#items+1] = '<function>'
        end
      elseif typ == 'function' then items[#items+1] = '<function>'
      elseif typ == 'boolean' or typ == 'nil' then items[#items+1] = tostring(v)
      elseif v   == null then items[#items+1] = 'null'
    end
  end

  -- In case we got an error object, ignore everything
  if not msg.msg then
    msg.msg = concat(items, '')
  end

  -- Write to nginx and return
  if level == JUSTSAY then
    say(msg.msg)
  elseif level >= STDERR and level < JUSTSAY then
    -- Write to TOOLS's log
    local write, err = logfile:write(encode(msg) .. '\n')
    if not write then
      ngx_log(ERR, 'Error writting to TOOLS logfile:', err)
    end
    local flush, err = logfile:flush(true)
    if not flush then
      ngx_log(ERR, 'Error flushing to TOOLS logfile:', err)
    end
    ngx_log(level, msg.msg)
  end
  return msg.msg
end

return log
