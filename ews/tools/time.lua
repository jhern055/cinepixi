--
-- PNG's Time Functions
--

-- locals
local ngx = ngx
local format = string.format
local tonumber = tonumber
local time = {}

--- Generates a random id of variable length
-- @param len Length of the id, default to len_default
function time.took()
  local request_time = ( ngx.now() - ngx.req.start_time() ) * 1000
  return tonumber(format('%.3f', request_time), 10)
end

return time
