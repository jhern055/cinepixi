--
-- PNG's Random/Crypto Utilities
--

-- dependencies
local random = require 'resty.random'
local log    = require 'tools.log'

-- locals
local ngx = ngx
local random_bytes = random.bytes
local sub = string.sub
local re_gsub = ngx.re.gsub
local base64 = ngx.encode_base64
local traceback = debug.traceback

--- Generates a random id of variable length
-- @param len Length of the id, default to len_default
function random.id(len)
  local len_default = 17
  if not len then len = len_default end
  if type(len) ~= 'number' then 
    return nil, log(ngx.ERR, {reason='Invalid length: ' .. type(len), traceback=traceback()} )
  end
  local token = random_bytes(len*2, true)
  -- remove mistakable chars: 10IOUVl+/=
  return sub( re_gsub( base64(token), '[10IOUVl+/=]+', '', 'i'), 1, len )
end

return random
