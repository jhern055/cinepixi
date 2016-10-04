-- locals, prevent global scope lookup, grouped by module
local tonumber, tostring   = tonumber, tostring
local insert, concat, sort = table.insert, table.concat, table.sort
local pairs, ipairs, type  = pairs, ipairs, type
local error = error

local patterns = {
  ['string']   = 'string',
  ['number']   = 'number',
  ['boolean']  = 'boolean',
  ['table']    = 'table',
  ['nil']      = 'nil',
  ['null']     = 'userdata', -- ngx.null
  ['userdata'] = 'userdata'  -- ngx.null
}

local function check(value, pattern)
  local is_value_table   = type(value)   == 'table'
  local is_pattern_table = type(pattern) == 'table'

  -- Validate pattern
  if is_pattern_table then
    for i, v in ipairs(pattern) do
      if not patterns[v] then
        error('Invalid type #' .. i .. ': ' .. tostring(pattern[i]))
      end
    end
  else
    if not pattern or not patterns[pattern] then
      error('Invalid type: ' .. tostring(pattern))
    end
  end

  if not is_pattern_table and type(value) ~= patterns[pattern] then
    error('Expected type ' .. patterns[pattern] .. ' and got ' .. type(value))
  end

  if is_value_table and is_pattern_table then
    for i, v in ipairs(pattern) do
      if type(value[i]) ~= patterns[pattern[i]] then
        error('Expected type ' .. patterns[pattern[i]] .. 
              ' in arg #' .. i .. ' and got ' .. type(value[i]))
      end
    end
  end

  return true
end

return check
