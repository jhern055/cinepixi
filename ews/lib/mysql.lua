-- load config
local config = require 'config'
local log    = require 'tools.log'
local mysql  = require 'resty.mysql'

local ngx = ngx
local traceback = debug.traceback
local tostring = tostring

local _M = {}
function _M.new(options)
  if not ngx.ctx.mysql then
    -- instantiate mysql
    local db, err = mysql:new()
    if not db then
      log(ngx.ERR, {reason='Failed to instantiate mysql:' .. tostring(err), traceback=traceback()})
      ngx.exit(ngx.HTTP_INTERNAL_SERVER_ERROR)
    end
    -- set connection timeout
    local timeout
    if config.db and config.db.timeout then timeout = config.db.timeout end
    if options   and options.timeout   then timeout = options.timeout   end
    db:set_timeout(timeout or 10000)

    -- connect
    local ok, err, errno, sqlstate = db:connect(options or config.db)
    if not ok then
      log(ngx.ERR, { reason='Failed to connect to mysql: ' .. tostring(err) ..
                            ', errno=' .. tostring(errno) .. ', sqlstate=' .. tostring(sqlstate),
                     traceback=traceback() })
      ngx.exit(ngx.HTTP_INTERNAL_SERVER_ERROR)
    end
    ngx.ctx.mysql = db
  end
  return ngx.ctx.mysql
end

setmetatable(_M, { __call = function(_, ...) return _M.new(...) end })
return _M
