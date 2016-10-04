--
-- Microframework for testing Openresty + LuaJIT
-- Author: JP Espinosa, PCEL 2014
--
local _ = _
local ngx, log = ngx, log
local say, now, null = ngx.say, ngx.now, ngx.null
local encode, decode = cjson.encode, cjson.decode
local format, gsub, match = string.format, string.gsub, string.match
local tonumber, tostring, loadstring = tonumber, tostring, loadstring
local insert, concat, sort = table.insert, table.concat, table.sort
local pairs, ipairs, type = pairs, ipairs, type
local len = string.len

local test_string = function(test_code, expected)
  -- trim and add return to original code
  local pretty_code = gsub(gsub(test_code,'%s+$', ''), '^%s+', '')
  local fixed_code  = format('return %s', pretty_code)
  local test_fun = loadstring(fixed_code)

  local extra_line = ''
  if len(pretty_code) >= 70 then
    extra_line = '\n----------------------------------------------------------------------'
  end

  -- test code
  local ok, res = pcall(test_fun)
  local pretty_res, pretty_expected = res, expected
  if ok or expected == nil then
    if type(res) == 'table' then
      local _, json = pcall(encode, res)
      pretty_res = json
    end
    if type(expected) == 'table' then
      local _, json = pcall(encode, expected)
      pretty_expected = json
    end
    if not ok then res, pretty_res = nil, nil end
    local result
    if not _.is_equal(res,expected) then 
      log(ngx.ERR, pretty_code, ' => [test failed]:', res)
      result = ' Result: FAIL'
    else
      result = ' Result: PASS'
    end
    return log(format('%-70s', pretty_code), extra_line, 
               ' => E=', format('%-10s', pretty_expected), 
               ' R=', format('%-10s', pretty_res), 
               result)
  else
    return log(ngx.ERR, pretty_code, ' => code error:', res)
  end
end

local test_function = function(title, testfun)
  local extra_line = ''
  if len(title) >= 70 then
    extra_line = '\n----------------------------------------------------------------------'
  end
  -- test code
  local ok, res = pcall(testfun)
  local pretty_res = res

  if ok then
    if type(res) == 'table' then
      local _, json = pcall(encode, res)
      pretty_res = json
    end
    return log(format('TEST: %-70s', title), 
           extra_line, 
           ' R=', format('%-10s', pretty_res), 
           ' Result: PASS')
  else
    log(ngx.ERR, title, ' => throw error:', res)
    return log(format('TEST: %-70s', title), 
           extra_line, 
           ' R=', format('%-10s', pretty_res), 
           ' Result: FAIL')
  end
end

local test = function (test_code, expected)
  if type(expected) ~= 'function' then
    return test_string(test_code, expected)
  end
  return test_function(test_code, expected)
end

return test
