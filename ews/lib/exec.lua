
-- Execute an OS command and return result
local function exec(cmd, raw)
  local f = assert(io.popen(cmd, 'r'))
  local s = assert(f:read('*a'))
  f:close()
  if raw then return s end
  s = string.gsub(s, '[\n\r]+', '') -- Remove new lines
  return s
end

return exec
