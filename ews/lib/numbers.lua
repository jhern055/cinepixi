
-- numbers
local _M = {}
local floor = math.floor
local ceil = math.ceil
local type, ipairs = type, ipairs

-- Round a number towards 0 with precision
function _M.round (num, idp)
  local mult = 10^(idp or 0)
  if num >= 0 then 
    return floor(num * mult + 0.5) / mult
  else
    return ceil(num * mult - 0.5) / mult
  end
end

-- Calculate mean for table values
function _M.mean (t)
  if type(t) ~= 'table' or #t == 0 then
    return 0
  end
  local sum = 0
  local items = 0
  for i, v in ipairs(t) do 
    if type(v) == 'number' then
      sum = sum + v
      items = items + 1
    end
  end
  return (sum / items)
end

-- Return module
return _M
