Date = {}

-- Convert a SQL now() format to Lua date object
function Date.dateSql(dateString)
  if dateString == nil then return nil end
  -- 2014-07-01 11:39:03 --> No offset support
  local pattern = "(%d+)%-(%d+)%-(%d+)%s(%d+)%:(%d+)%:(%d+)"
  local year, month, day, hour, minute, seconds = dateString:match(pattern)
  return os.time({year=year, month=month, day=day, hour=hour, min=minute, sec=seconds}) * 1000
end

return Date
