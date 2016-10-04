-------------------------------------------------------------------------------
--
-- package: '_' = Underscore
--
-- Functional Programming for LuaJIT
--
-- author : JP Espinosa, PCEL 2014
--
-------------------------------------------------------------------------------
-- 
-- Inspired from:
--    http://underscorejs.org/
--    https://github.com/jashkenas/underscore/blob/master/underscore.js
--    https://github.com/Yonaba/Moses/blob/master/moses.lua
--    https://github.com/mirven/underscore.lua/blob/master/lib/underscore.lua
--    https://github.com/rtsisyk/luafun
--    http://rtsisyk.github.io/luafun/genindex.html
--    http://jhusain.github.io/learnrx/
--
-------------------------------------------------------------------------------
local fun = require('fun')

-- locals
local pairs, tonumber, next = pairs, tonumber, next
local setmetatable, getmetatable, unpack = setmetatable, getmetatable, unpack
local min, floor = math.min, math.floor
local insert, remove, sort = table.insert, table.remove, table.sort
local format, len, gsub = string.format, string.len, string.gsub
local now, null, rawget = ngx.now, ngx.null, rawget
local INIFITY = math.huge

-- API Container
local _  = { __IS_UNDERSCORE = true }
local mt = { __index = _ }

-------------------------------------------------------------------------------
-- privates
-------------------------------------------------------------------------------

--- Underscore is self?
-- Check if an object is underscore object
-- @param self (any)
-- @return boolean
local is_ = function (t)
  return type(t) == 'table' and rawget(t, '__IS_UNDERSCORE') == true
end


--- Has underscore metatable
-- Check if a table has underscore table
-- @param self (any)
-- @return boolean
local has_ = function (t)
  local meta = getmetatable(t)
  return meta and meta.__index and meta.__index.__IS_UNDERSCORE == true
end


--- Has underscore metatable
-- Check if a table has underscore table
-- @param self (any)
-- @return boolean
local put_ = function (t)
  return type(t) == 'table' and not getmetatable(t) and setmetatable(t, mt) or t
end


-- table.new
local ok, new_tab = pcall(require, 'table.new')
if not ok then
  new_tab = function(narr, nrec) return {} end
end


-------------------------------------------------------------------------------
-- api / wrapper
-------------------------------------------------------------------------------


-- A mostly-internal function to generate callbacks that can be applied
-- to each element in a collection, returning the desired result — either
-- identity, an arbitrary callback, a property matcher, or a property accessor.
_.cb = function (self, value)
  if not is_(self) then value = self end
  if value == nil then return _.identity end
  if _.is_function(value) then return value end
  if _.is_object(value) then return _.matches(value) end
  return _.property(value)
end


-- Keep the identity function around for default iteratees.
_.identity = function (self, value)
  if not is_(self) then value = self end
  return value
end


_.property = function (self, key)
  if not is_(self) then key = self end
  return function(obj)
    if obj == nil then return nil end
    return obj[key]
  end
end


-- Predicate-generating functions. Often useful outside of Underscore.
_.constant = function(self, value)
  if not is_(self) then value = self end
  return function()
    return value
  end
end


-- Should always return nil
_.noop = function() return end


-- Perform a deep comparison to check if two objects are equal.
_.is_equal = function (self, object, other)
  if not is_(self) then object, other = self, object end
  if object == other then return true end
  if type(object) == 'table' and type(other) == 'table' then
    if _.size(object) ~= _.size(other) then
      return false
    end
    if fun.any(function(v, k)
        return not _.is_equal(object[k], other[k])
       end, object) then
      return false
    end
    return true
  end
  return false
end
_.isEqual   = _.is_equal
_.areEqual  = _.is_equal
_.are_equal = _.is_equal


-- Is a given object a table?
_.is_table = function (self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'table'
end
_.isTable = _.is_table


-- Is a given object a string?
_.is_string = function (self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'string'
end
_.isString = _.is_string


-- Is a given object a boolean?
_.is_boolean = function (self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'boolean'
end
_.isBoolean = _.is_boolean

-- Is a given object a null?
_.is_null = function (self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'nil' or obj == null
end
_.isNull = _.is_null

-- Is a given object a boolean?
_.is_function = function (self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'function'
end
_.isFunction = _.is_function


-- Is a given object a number?
_.is_number = function (self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'number'
end
_.isNumber = _.is_number


-- Is a given object a finite number?
_.is_finite = function (self, obj)
  if not is_(self) then obj = self end
  if type(obj) ~= 'number' then return false end
  return obj > -INIFITY and obj < INIFITY
end
_.isFinite = _.is_finite


-- Is a given object a 'nan' (not-a-number)?
_.is_nan = function (self, obj)
  if not is_(self) then obj = self end
  --only NaNs will have the property of not being equal to themselves
  if obj ~= obj then return true end
  --only a number can not be a number
  if type(obj) ~= 'number' then return false end
  --Slower, but works around the three above bugs in LUA
  if tostring(obj) == tostring((-1)^0.5) then return true end
  return false
end
_.isNan = _.is_nan


-- Is a given object a number?
_.is_integer = function (self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'number' and obj % 1 == 0
end
_.isInteger = _.is_integer


-- Check if a table is a mixed object: has indexes and is hashed
_.is_mixed = function(self, obj)
  if not is_(self) then 
    obj = self
  end
  if type(obj) == 'table' and next(obj) then
    -- Using a custom iterator to iterate over a array-hash mixed table
    -- see http://rtsisyk.github.io/luafun/basic.html#fun.iter
    local pairs_gen = pairs({ a = 0 }) -- get the generating function from pairs
    local map_gen = function(tab, key)
        local value
        key, value = pairs_gen(tab, key)
        return key, key, value
    end
    local first_index = type(next(obj))
    return fun.any(function(_, k)
      if type(k) == first_index then return false end
      return true
    end, map_gen, obj, 0)
  end
  return false
end


-- Is a given object an array (indexed table)?
_.is_array = function (self, obj, deep)
  if not is_(self) then 
    obj, deep = self, obj
  end
  if type(obj) == 'table' then
    if #obj > 0 then
      if deep and _.is_mixed(obj) then return false end
      return true
    elseif #obj == 0 and not next(obj) then -- empty table
      return true
    end
  end
  return false
end
_.isArray = _.is_array


-- Is a given object an object (hashed table)?
_.is_object = function (self, obj)
  if not is_(self) then obj = self end
  if type(obj) == 'table' then
    if #obj == 0 and next(obj) then
      return true
    elseif #obj > 0 then
      return false
    elseif #obj == 0 and not next(obj) then -- empty table
      return true
    end
  end
  return false
end
_.isObject = _.is_object
_.is_hash  = _.is_object
_.isHash   = _.is_object


-- Is a given array or object empty?
-- An "empty" object has no enumerable own-properties.
_.is_empty = function (self, obj)
  if not is_(self) then obj = self end
  if not _.is_table(obj) then return true end
  if _.is_array(obj)  then return #obj == 0 end
  if _.is_object(obj) then return next(obj) == nil end
  return true
end
_.isEmpty = _.is_empty


-- Check if an object is iterable
_.is_iterable = function(self, obj)
  if not is_(self) then obj = self end
  return type(obj) == 'table' or type(obj) == 'function' or type(obj) == 'string'
end


-- Returns a predicate for checking whether an object has a given set of `key:value` pairs.
_.matches = function (self, attrs)
  if not is_(self) then attrs = self end
  local p = _.pairs(attrs)
  local l = #p
  return function(obj)
    if type(obj) ~= 'table' then return false end
    if l == 0 then return true end
    return fun.all(function(index)
      local pair, key = p[index], p[index][1]
      if obj[key] == nil or pair[2] ~= obj[key] then
        return false
      end
      return true
    end, fun.range(1, l))
  end
end


-- Returns a negated version of the passed-in predicate.
_.negate = function(self, predicate)
  if not is_(self) then predicate = self end
  return function(...)
    return not predicate(...)
  end
end


-- Convert an object into a list of `[key, value]` pairs.
_.pairs = function (self, obj)
  if not is_(self) then obj = self end
  local keys = _.keys(obj)
  local results = put_({})
  fun.each(function(v)
    results[#results+1] = { v, obj[v] }
  end, keys)
  return results
end


-- Retrieve the names of an object's own properties.
_.keys = function (self, obj)
  if not is_(self) then obj = self end
  if not _.is_object(obj) then return put_({}) end
  local keys = _.map(obj, function(_, k) return tostring(k) end)
  sort(keys)
  return keys
end


-- Retrieve the values of an object's properties.
_.values = function (self, obj)
  if not is_(self) then obj = self end
  return _.map(_.keys(obj), function(v)
    return obj[v]
  end)
end


-- The indexOf() method searches the array for the specified item, 
-- and returns its position.
_.index_of = function (self, array, value, isSorted)
  if not is_(self) then
    array, value, isSorted = self, array, value
  end
  if isSorted then error('isSorted on _.index_of is not yet implemented. patch welcome :)') end
  if not _.is_array(array) then return -1 end
  return fun.index(value, array) or -1
end
_.indexOf = _.index_of
_.indexof = _.index_of


--- Returns the index of the last occurrence of a given value
_.last_index_of = function (self, array, value, fromIndex)
  if not is_(self) then
    array, value, fromIndex = self, array, value
  end
  if fromIndex then error('fromIndex on _.last_index_of is not yet implemented. patch welcome :)') end
  if not _.is_array(array) then return -1 end
  local key = _.index_of(_.reverse(array),value)
  if key and key ~= -1 then return #array-key+1 end
  return -1
end
_.lastIndexOf = _.last_index_of


-- Does the object contain the given key? 
_.has = function (self, obj, key)
  if not is_(self) then
    obj, key = self, obj
  end
  if type(obj) == 'table' then
    return obj[key] ~= nil
  end
  return false
end


-- Returns true if the value is present in the list. Uses indexOf internally if list is an Array
_.contains = function (self, list, value)
  if not is_(self) then
    list, value = self, list
  end
  if _.is_object(list) then list = _.values(list) end
  return _.index_of(list, value) >= 0
end


-- Invoke a method (with arguments) on every item in a collection.
_.invoke = function(self, obj, method, ...)
  local args = {...}
  if not is_(self) then
    args = _.concat({method}, args)
    obj, method = self, obj
  end
  return _.map(obj, function(value)
      return method(value, unpack(args))
    end)
end


-- Run a function **n** times.
_.times = function(self, n, iteratee)
  if not is_(self) then
    n, iteratee = self, n
  end
  local accum = _.table(n)
  if not n or n <= 0 then return accum end
  fun.each(function(i)
    accum[#accum+1] = iteratee(i)
  end, fun.range(n))
  return accum
end


-- The push() method adds new items to the end of an array, and returns the new length.
_.push = function (self, ...)
  local args = {...}
  local obj = args[1]
  if not is_(self) then
    obj = self
  else
    remove(args,1)
  end
  if not _.is_array(obj) then return obj end
  fun.each(function(v)
      obj[#obj+1] = v
    end, args)
  return #obj
end


-- The pop() method removes the last element from an array and returns that element
_.pop = function (self, array)
  if not is_(self) then array = self end
  if not _.is_table(array) then return nil end
  local last = array[#array]
  local ok = pcall(remove, array)
  if ok then return last end
  return nil
end


-------------------------------------------------------------------------------
-- Generators returns iterators like ipairs() or pairs() (`for x in iterator()`)
-------------------------------------------------------------------------------

--  The iterator to create arithmetic progressions
-- @param start (number) – an endpoint of the interval
-- @param stop (number) – an endpoint of the interval
-- @param step (number) – a step
-- @return array with range
_.range = function (self, start, stop, step)
  if not is_(self) then 
    start, stop, step = self, start, stop
  end
  return put_(fun.totable(fun.range(start, stop, step)))
end


-------------------------------------------------------------------------------
-- Slicing -- This section contains functions to make subsequences from iterators
-------------------------------------------------------------------------------

-- Returns the first element of an array. 
-- Passing n will return the first n elements of the array.
-- extra is needed to work with _.map
_.first = function (self, array, n, extra)
  if not is_(self) then
    array, n, extra = self, array, n
  end
  if array == nil then return nil end
  if not n or extra then
    return put_( fun.head(array) )
  end
  if _.is_number(n) and n < 0 then return _.table() end
  return put_( fun.totable( fun.take(n, array) ) )
end
_.head = _.first
_.take = _.first


-- Returns the last element of an array. 
-- Passing n will return the last n elements of the array.
_.last = function (self, array, n, extra)
  if not is_(self) then
    array, n, extra = self, array, n
  end
  if not _.is_array(array) then return nil end
  if n == nil then n = 1 end
  if n == 1 or extra then return array[#array] end
  if n < 1 then return _.table() end
  local start = #array - n + 1
  local ends  = #array
  if start < 1 then start = 1 end
  return _.slice(array, start, ends)
end


-- Returns the rest of the elements in an array. 
-- Pass an index to return the values of the array from that index onward
_.rest = function (self, array, index, extra)
  if not is_(self) then
    array, index, extra = self, array, index
  end
  if not index or extra then
    return put_( fun.totable( fun.tail(array) ) )
  end
  return put_( fun.totable( fun.drop(index, array) ) )
end
_.tail = _.rest
_.drop = _.rest

-------------------------------------------------------------------------------
-- Methods
-------------------------------------------------------------------------------
-- each iterates over a list of elements, 
-- yielding each in turn to an iteratee function
_.each = function (self, list, iteratee)
  if not is_(self) then
    list, iteratee = self, list
  end
  local iter = function(v, k)
    put_(v)
    return iteratee(v, k, list)
  end
  fun.each(iter, list)
  return put_(list)
end


-- Map accepts the projection function to be applied 
-- to each item in the source array, and returns the projected array.
_.map = function (self, list, iteratee)
  if not is_(self) then
    list, iteratee = self, list
  end
  iteratee = _.cb(iteratee)
  local results = put_({})
  local iter = function (v, k)
    put_(v)
    return iteratee(v, k, list)
  end
  fun.map(iter, list):each(function(item) results[#results+1] = item end)
  return results
end


-- Convenience version of a common use case of `map`: fetching a property
_.pluck = function (self, list, propertyName)
  if not is_(self) then 
    list, propertyName = self, list
  end
  return _.map(list, function (v) return v[propertyName] end)
end


-- The filter() function accepts a predicate. A predicate is a 
-- function that accepts an item in the array, and returns a 
-- boolean indicating whether the item should be retained in the new array.
_.filter = function (self, list, predicate)
  if not is_(self) then 
    list, predicate = self, list
  end
  predicate = _.cb(predicate)
  local results = put_({})
  local filter = function (v, k)
    put_(v)
    return predicate(v, k, list)
  end
  fun.each(function(v)
    results[#results+1] = v
  end, fun.filter(filter, list))
  return results
end


-- Looks through each value in the list, returning an array 
-- of all the values that contain all of the key-value pairs listed in properties
_.where = function (self, list, properties)
  if not is_(self) then 
    list, properties = self, list
  end
  return _.filter(list, _.matches(properties));
end


-- Convenience version of a common use case of `find`: getting the first object
-- containing specific `key:value` pairs.
_.find_where = function(self, obj, attrs)
  if not is_(self) then 
    obj, attrs = self, obj
  end
  return _.find(obj, _.matches(attrs))
end
_.findWhere = _.find_where


-- Returns true if any of the values in the list pass the predicate truth test. 
-- Short-circuits and stops traversing the list if a true element is found.
_.any = function (self, list, predicate)
  if not is_(self) then
    list, predicate = self, list
  end
  predicate = _.cb(predicate)
  local filter = function (v, k)
    put_(v)
    return predicate(v, k, list)
  end
  return fun.any(filter, list)
end
_.some = _.any


-- Returns true if all of the values in the list pass the predicate truth test.
_.all = function (self, list, predicate)
  if not is_(self) then
    list, predicate = self, list
  end
  if list == nil then return true end
  predicate = _.cb(predicate)
  return fun.all(function(v, k)
    return predicate(v, k, list)
  end, list)
end
_.every = _.all


--- Flattens a nested array (the nesting can be to any depth). 
--  strict = only allow arrays
_.flatten = function (self, array, shallow, strict)
  if not is_(self) then 
    array, shallow, strict = self, array, shallow
  end
  local results = put_({})
  if not _.is_array(array) then return results end
  fun.each(
    function(v)
      if _.is_array(v) then
        if not shallow then v = _.flatten(v, shallow) end
        fun.each(function(v)
          results[#results+1] = v
        end, v)
      elseif not strict then
        results[#results+1] = v
      end
    end, array)
  return results
end


-- The reduce() method applies a function against an accumulator 
-- and each value of the array (from left-to-right) has to reduce it to a single value.
_.reduce = function (self, list, iteratee, memo)
  if not is_(self) then 
    list, iteratee, memo = self, list, iteratee
  end
  if list == nil or not _.is_array(list, true) then return memo end
  if not memo then
    if #list == 0 then error('Reduce of empty array with no initial value') end
    if #list == 1 then return list[1] end
  end
  local iter = function(memo, v)
    if not memo then return v end -- default value for memo if missing
    return iteratee(memo, v)
  end
  return put_( fun.foldl(iter, memo, list) )
end


-- The right-associative version of reduce, also known as `foldr`.
_.reduce_right = function (self, list, iteratee, memo)
  if not is_(self) then 
    list, iteratee, memo = self, list, iteratee
  end
  if list == nil or not _.is_array(list, true) then return memo end
  return _.reduce(_.reverse(list), iteratee, memo)
end
_.reduceRight = _.reduce_right


-- Safely create an array from anything iterable
_.to_array = function (self, iterable)
  if not is_(self) then iterable = self end
  if _.is_array(iterable)  then return _.clone(iterable) end
  if _.is_string(iterable) then return put_(fun.totable(iterable)) end
  if _.is_object(iterable) then return _.values(iterable) end
  return _.table()
end
_.to_table = _.to_array
_.toArray  = _.to_array
_.toTable  = _.to_array


-- Zip together multiple lists into a single array -- elements that share
-- an index go together.
_.zip = function (self, ...)
  local args = {...}
  if _.is_empty(args) then return put_({}) end
  if not is_(self) then
    args = _.concat({self}, args)
  end
  local results = put_({})
  fun.each(function(v, k)
    results[k] = _.pluck(args, k);
  end, args)
  return results
end


-- Return the number of values in the list
_.size = function (self, t, deep)
  if not is_(self) then 
    t, deep = self, t
  end
  if deep and type(t) == 'table' then
    -- Using a custom iterator to iterate over a array-hash mixed table
    -- see http://rtsisyk.github.io/luafun/basic.html#fun.iter
    local pairs_gen = pairs({ a = 0 }) -- get the generating function from pairs
    local map_gen = function(tab, key)
        local value
        key, value = pairs_gen(tab, key)
        return key, key, value
    end
    local i = 0
    fun.each(function(_) i = i + 1 end, map_gen, t, 0)
    return i
  elseif _.is_array(t) then
    local tlen = #t
    fun.each(function(i)
      if t[i] ~= nil then
        tlen = i
      end
    end, fun.range(tlen+1, tlen+10))
    return tlen
  elseif _.is_object(t) then
    return _.size(_.keys(t))
  elseif _.is_string(t) then
    return len(t)
  end
  return 0
end
_.count = _.size


-- Extend a given object with all the properties in passed-in object(s).
_.extend = function (self, ...)
  local args = {...}
  local obj = args[1]
  if not is_(self) then
    obj = self
  else
    remove(args, 1)
  end
  if not _.is_object(obj) then return obj end
  fun.each(function(v)
    fun.each(function(v, k)
      obj[k] = v
    end, v)
  end, args)
  return obj
end


-- Concat tables
_.concat = function (self, ...)
  local args = {...}
  local array = args[1]
  if not is_(self) then
    array = self
  else
    remove(args, 1)
  end
  if _.is_array(array) then
    fun.each(function(v)
      fun.each(function(v)
        array[#array+1] = v
      end, v)
    end, args)
  end
  return array
end
_.append = _.concat


-- A deep copy for a table
_.clone = function (self, orig)
  if not is_(self) then orig = self end
  local copy
  if _.is_table(orig) then
    copy = {}
    fun.each(function(v, k)
      copy[_.clone(k)] = _.clone(v)
    end, orig)
    put_(setmetatable(copy, getmetatable(orig))) -- copy original mt or put underscore's mt
  else -- number, string, boolean, etc
    copy = orig
  end
  return copy
end


-- Split a string into an array of substrings, supports Lua patterns
_.split = function (self, str, re, plain, n)
  if not is_(self) then 
    str, re, plain, n = self, str, re, plain
  end
  local find, sub, insert = string.find, string.sub, table.insert
  local i1,ls = 1,put_({})
  if not re then re = '%s+' end
  if re == '' then return put_({str}) end
  if re == '.' then re = '%.' end
  while true do
    local i2,i3 = find(str,re,i1,plain)
    if not i2 then
      local last = sub(str,i1)
      if last ~= '' then insert(ls,last) end
      if #ls == 1 and ls[1] == '' then
        return put_({})
      else
        return ls
      end
    end
    insert(ls,sub(str,i1,i2-1))
    if n and #ls == n then
      ls[#ls] = sub(str,i1)
      return ls
    end
    i1 = i3+1
  end
end


--- Produces a duplicate-free version of a given array.
_.uniq = function (self, array, isSorted, iteratee)
  if not is_(self) then
    array, isSorted, iteratee = self, array, isSorted
  end
  if array == nil then return put_({}) end
  if not _.is_boolean(isSorted) then
    iteratee = isSorted
    isSorted = false
  end
  if iteratee ~= nil then iteratee = _.cb(iteratee) end
  local result = {}
  local seen = {}

  fun.each(function(v, k)
    local computed = v
    if iteratee then
      computed = iteratee(v, k, array)
      if not _.contains(seen, computed) then
        insert(seen, computed)
        insert(result, v)
      end
    elseif not _.contains(result, v) then
      insert(result, v)
    end
  end, array)

  return result
end
_.unique = _.uniq


-- Produce an array that contains the union: each distinct element from all of
-- the passed-in arrays.
_.union = function(...)
  return _.uniq( _.flatten({...}, true, true) )
end


--- Produces a duplicate-free version of a given array.
_.uniq_arg = function (self, ...)
  local arg = {...}
  if not is_(self) then
    arg = _.concat({self}, arg)
  end
  return _.uniq(arg)
end
_.uniq_args = _.uniq_arg


--- Checks if a given array contains distinct values. 
-- Such an array is made of distinct elements, which only occur once in this array
_.is_uniq = function (self, array)
  if not is_(self) then array = self end
  return _.is_equal(array, _.uniq(array))
end
_.is_unique = _.is_uniq
_.isunique  = _.is_uniq


-- Take the difference between one array and a number of other arrays
-- Only the elements present in just the first array will remain
_.diff = function (self, ...)
  local args = {...}
  local array = args[1]
  if not is_(self) then
    array = self
  else
    remove(args, 1)
  end
  args = _.flatten(args, true)
  return _.filter(array, function(value)
    return not _.contains(args, value)
    end)
end
_.difference = _.diff


-- Produce an array that contains every item shared between all the
-- passed-in arrays.
_.intersection = function (self, ...)
  local args = {...}
  local array = args[1]
  if not is_(self) then
    array = self
  else
    remove(args, 1)
  end
  if array == nil then return put_({}) end
  local result = put_({})
  fun.each(function(item)
    if not _.contains(result, item) then
      if _.all(args, function(arg) return _.contains(arg, item) end) then
        result[#result+1] = item
      end
    end
  end, array)
  return result
end


-- Return an array of duplicates values
_.duplicates = function (self, ...)
  local arrays = {...}
  if not is_(self) then
    arrays = _.concat({self}, arrays)
  end
  arrays = _.flatten(arrays, true)
  return _.uniq( _.filter(arrays, function(v)
    local found = 0
    return _.any(arrays, function(v2)
      if v == v2 then found = found + 1 end
      return found == 2
    end)
  end) )
end


-- Returns the maximum value in list. If an iteratee function is provided, 
-- it will be used on each value to generate the criterion by which the 
-- value is ranked. -Infinity is returned if list is empty, 
-- so an isEmpty guard may be required
_.max = function (self, iterable, cmp) 
  if not is_(self) then
    iterable, cmp = self, iterable
  end
  if not cmp then
    local ok, max = pcall(fun.max, iterable)
    if not ok then return -INIFITY end
    return max
  else
    if not _.is_iterable(iterable) then return -INIFITY end
    local result = -INIFITY
    local lastComputed = -INIFITY
    local computed
    cmp = _.cb(cmp)
    fun.each(function(value, index)
      computed = cmp(value, index)
      if (computed > lastComputed or computed == -INIFITY and result == -INIFITY) then
        result = value
        lastComputed = computed
      end
    end, iterable)
    return result
  end
end
_.maximum = _.max


-- Returns the minimum value in list. If an iteratee function is provided, 
-- it will be used on each value to generate the criterion by which the 
-- value is ranked. Infinity is returned if list is empty, so an isEmpty 
-- guard may be required
_.min = function (self, iterable, cmp) 
  if not is_(self) then
    iterable, cmp = self, iterable
  end
  if not cmp then
    local ok, min = pcall(fun.min, iterable)
    if not ok then return INIFITY end
    return min
  else
    if not _.is_iterable(iterable) then return INIFITY end
    local result = INIFITY
    local lastComputed = INIFITY
    local computed
    cmp = _.cb(cmp)
    fun.each(function(value, index)
      computed = cmp(value, index)
      if (computed < lastComputed or computed == INIFITY and result == INIFITY) then
        result = value
        lastComputed = computed
      end
    end, iterable)
    return result
  end
end
_.minimum = _.min


-- If regexp_or_predicate is string then the parameter is used as a 
-- regular expression to build filtering predicate. Otherwise the 
-- function is just an alias for filter().
_.grep = function (self, str_predicate, iterable)
  if not is_(self) then
    str_predicate, iterable = self, str_predicate
  end
  if type(str_predicate) == 'string' then
    return put_( fun.totable(fun.grep(str_predicate, iterable)) )
  else 
    return _.filter(iterable, str_predicate)
  end
end


-- Return the first value which passes a truth test. Aliased as `detect`
_.find = function(self, list, predicate)
  if not is_(self) then
    list, predicate = self, list
  end
  predicate = _.cb(predicate)
  local key
  local iter = function(v, k)
    if predicate(v, k, list) then
      key = k
      return true
    end
    return false
  end
  fun.any(iter, list)
  if key then 
    return list[key]
  end
end
_.detect = _.find


-- Returns a random integer between min and max, inclusive.
-- If you only pass one argument, it will return a number between 0 and that number.
_.random = function(self, min, max)
  if not is_(self) then
    min, max = self, min
  end
  if min == nil and max == nil then
    return math.random()
  end
  if max == nil then
    max = min
    min = 0
  end
  return math.random(min, max)
end


-- Shuffle a collection, using the modern version of the
-- [Fisher-Yates shuffle](http://en.wikipedia.org/wiki/Fisher–Yates_shuffle)
_.shuffle = function(self, obj)
  if not is_(self) then obj = self end
  local set
  if _.is_array(obj) then 
    set = obj
  else
    set = _.values(obj)
  end
  local length = #set
  local shuffled = {}
  fun.each(function(index)
    local rand = _.random(1, index)
    if rand ~= index then
      shuffled[index] = shuffled[rand]
    end
    shuffled[rand] = set[index]
  end, fun.range(1,length))
  return put_(shuffled)
end


-- Sample **n** random values from a collection.
-- If **n** is not specified, returns a single random element.
_.sample = function(self, obj, n)
  if not is_(self) then 
    obj, n = self, obj
  end
  if n == nil then
    if _.is_object(obj) then obj = _.values(obj) end
    return obj[_.random(1, #obj)]
  elseif n <= 0 then
    return _.table()
  end
  return _.shuffle(obj):slice(1, math.max(1, n))
end


-- The `_.slice` method returns the selected elements 
-- in an array, as a new array object
_.slice = function(self, array, start, ends)
  if not is_(self) then
    array, start, ends = self, array, start
  end
  local sliced = put_({})
  if _.is_object(array) then array = _.values(array) end
  if not _.is_finite(start) or start <= 0 then
    start = 1
  end
  if not _.is_finite(ends) or ends > #array then
    ends = #array
  end
  if start > ends then return sliced end
  if start == 1 and ends == #array then return put_(array) end
  fun.each(function(v)
    sliced[#sliced+1] = array[v]
  end, fun.range(start, ends))
  return sliced
end


-- Returns everything but the last entry of the array. 
-- Especially useful on the arguments object. 
-- Pass n to exclude the last n elements from the result.
_.initial = function (self, array, n, extra)
  if not is_(self) then
    array, n, extra = self, array, n
  end
  if not _.is_array(array) then return _.table() end
  if n == nil or extra then n = 1 end
  if n >= #array then return _.table() end
  return _.slice(array, 1, #array-n)
end


-- Create a table with underscore's metatable
_.table = function(self, t)
  if not is_(self) then t = self end
  if type(t) == 'table' then
    return put_(t)
  elseif type(t) == 'number' then
    return put_(new_tab(t,0))
  end
  return put_({})
end


-- Returns a copy of the array with all falsy values removed. 
-- In Lua, only boolean 'false'
_.compact = function (self, array)
  if not is_(self) then array = self end
  return _.filter(array, _.identity)
end


-- Return a version of the array that does not contain the specified value(s).
_.without = function(self, array, ...)
  local args = {...}
  if not is_(self) then
    args = _.concat({array}, args)
    array = self
  end
  return _.difference(array, unpack(args))
end


-- Converts lists into objects. Pass either a single array of `[key, value]`
-- pairs, or two parallel arrays of the same length -- one of keys, and one of
-- the corresponding values.
_.object = function(self, list, values)
  if not is_(self) then
    list, values = self, list
  end
  local result = _.table()
  if list == nil then return result end
  fun.each(function(v, i)
    if values then
      result[v] = values[i]
    else
      result[v[1]] = v[2]
    end
  end, list)
  return result
end


-- Return a table sorted
_.sort = function(self, t, iteratee)
  if not is_(self) then
    t, iteratee = self, t
  end
  table.sort(t, iteratee)
  return t
end


-- ngx.now wrap
_.now = function()
  return tonumber(gsub(format('%.3f', now()), '%.', ''), 10)
end


-- Return all the elements for which a truth test fails.
_.reject = function(self, obj, predicate)
  if not is_(self) then
    obj, predicate = self, obj
  end
  return _.filter(obj, _.negate(_.cb(predicate)))
end


--- Reverses values in a given array. The passed-in array should not be sparse.
-- @name reverse
-- @tparam table array an array
-- @treturn table a copy of the given array, reversed
_.reverse = function(self, array)
  if not is_(self) then array = self end
  local results = {}
  fun.each(function(i)
    results[#results+1] = array[i]
  end, fun.range(#array, 1, -1))
  return results
end


-- Split a collection into two arrays: one whose elements all satisfy the given
-- predicate, and one whose elements all do not satisfy the predicate.
_.partition = function(self, obj, predicate)
  if not is_(self) then 
    obj, predicate = self, obj
  end
  predicate = _.cb(predicate)
  local pass, fail = _.table(), _.table()
  fun.each(function(value, key)
    if predicate(value, key, obj) then
      pass[#pass+1] = value
    else
      fail[#fail+1] = value
    end
  end, obj)
  return {pass, fail}
end


--- Returns the index at which a value should be inserted. This returned index is determined so 
-- that it maintains the sort. If a comparison function is passed, it will be used to sort all
-- values.
_.sortedIndex = function(self, list, value, iteratee)
  if not is_(self) then
    list, value, iteratee, sort = self, list, value, iteratee
  end
  if iteratee then
    iteratee = _.cb(iteratee)
  else 
    iteratee = function(a,b) return a<b end
  end
  local i
  fun.any(function(idx)
    if not iteratee(list[idx],value) then 
      i = idx
      return true
    end
    return false
  end, fun.range(1,#list))
  return i or #list+1
end

-- This Lua library is a MySQL client driver for the ngx_lua nginx module:
-- Returns a var clean of SqlInjection.
-- https://github.com/openresty/lua-resty-mysql#sql-literal-quoting

_.cleanSqli = function(var)

  local var_unescape = ngx.unescape_uri(var)
  local quoted_var = ngx.quote_sql_str(var_unescape)

  return quoted_var
end


-- Split function 
-- _.split = function(s, pattern, maxsplit)
--   local pattern = pattern or ' '
--   local maxsplit = maxsplit or -1
--   local s = s
--   local t = {}
--   local patsz = #pattern
--   while maxsplit ~= 0 do
--     local curpos = 1
--     local found = string.find(s, pattern)
--     if found ~= nil then
--       table.insert(t, string.sub(s, curpos, found - 1))
--       curpos = found + patsz
--       s = string.sub(s, curpos)
--     else
--       table.insert(t, string.sub(s, curpos))
--       break
--     end
--     maxsplit = maxsplit - 1
--     if maxsplit == 0 then
--       table.insert(t, string.sub(s, curpos - patsz - 1))
--     end
--   end
--   return t
-- end

_.split=function(str, pat)
   local t = {}  -- NOTE: use {n = 0} in Lua-5.0
   local fpat = "(.-)" .. pat
   local last_end = 1
   local s, e, cap = str:find(fpat, 1)
   while s do
      if s ~= 1 or cap ~= "" then
   table.insert(t,cap)
      end
      last_end = e+1
      s, e, cap = str:find(fpat, last_end)
   end
   if last_end <= #str then
      cap = str:sub(last_end)
      table.insert(t, cap)
   end
   return t
end

_.implode=function(array, pat)
   local t = ""
    t=table.concat(array, pat)
   return t
end

-------------------------------------------------------------------------------
--[[ INDEX  ( NYI == NOT YET IMPLEMENTED )
-------------------------------------------------------------------------------


-- Collection Functions (Arrays or Objects)
-------------------------------------------------------------------------------
TESTED _.each (list, iteratee)
TESTED _.map (list, iteratee)
TESTED _.reduce (list, iteratee, memo)
TESTED _.reduce_right(list, iteratee, memo)
TESTED _.find (list, predicate)
TESTED _.filter (list, predicate)
TESTED _.where (list, properties)
TESTED _.find_where (obj, attrs)
TESTED _.reject(list, predicate)
TESTED _.all (list, predicate)
TESTED _.any (list, predicate)
TESTED _.contains (list, value)
TESTED _.invoke (obj, method, ...)
TESTED _.pluck (list, propertyName)
TESTED _.max (iterable, cmp) 
TESTED _.min (iterable, cmp) 
                                      * XXX NYI _.sortBy(list, iteratee)
                                      * XXX NYI _.groupBy(list, iteratee) 
                                      * XXX NYI _.indexBy(list, iteratee) 
                                      * XXX NYI _.countBy(list, iteratee)
TESTED _.shuffle (obj)
TESTED _.sample (obj, n)
TESTED _.to_array (iterable)
TESTED _.size (t)
TESTED _.partition(array, predicate)


-- Array Functions
-------------------------------------------------------------------------------
TESTED _.first(iterator, [n])
TESTED _.initial(array, [n]) 
TESTED _.last(array, [n]) 
TESTED _.rest(obj)
TESTED _.compact(array) 
TESTED _.flatten(array, [shallow])
TESTED _.without(array, ...) 
TESTED _.union(*arrays) 
TESTED _.intersection(*arrays) 
TESTED _.difference(array, *others) 
TESTED _.uniq(array, [isSorted], [iteratee])
TESTED _.zip(*arrays) 
TESTED _.object(list, [values]) 
TESTED _.index_of(array, value, [isSorted]) 
TESTED _.lastIndexOf(array, value, [fromIndex])
                                    * TESTS PENDING _.sortedIndex(list, value, [iteratee], [context]) 
TESTED _.range([start], stop, [step]) 


-- Function (uh, ahem) Functions
-------------------------------------------------------------------------------
                                    * XXX NYI _.bind(function, object, *arguments) 
                                    * XXX NYI _.bindAll(object, *methodNames) 
                                    * XXX NYI _.partial(function, *arguments) 
                                    * XXX NYI _.memoize(function, [hashFunction]) 
                                    * XXX NYI _.delay(function, wait, *arguments) 
                                    * XXX NYI _.defer(function, *arguments) 
                                    * XXX NYI _.throttle(function, wait, [options]) 
                                    * XXX NYI _.debounce(function, wait, [immediate]) 
                                    * XXX NYI _.once(function) 
                                    * XXX NYI _.after(count, function) 
                                    * XXX NYI _.before(count, function) 
                                    * XXX NYI _.wrap(function, wrapper) 
                                    * XXX NYI _.negate(predicate) 
                                    * XXX NYI _.compose(*functions) 


-- Object Functions
-------------------------------------------------------------------------------
TESTED _.keys(object) 
TESTED _.values(object) 
TESTED _.pairs(object) 
                                    * XXX NYI _.invert(object) 
                                    * XXX NYI _.functions(object) Alias: _.methods
TESTED _.extend(destination, *sources) 
                                    * XXX NYI _.pick(object, *keys) 
                                    * XXX NYI _.omit(object, *keys) 
                                    * XXX NYI _.defaults(object, *defaults) 
TESTED _.clone(object) 
                                    * XXX NYI _.tap(object, interceptor) 
TESTED _.has(object, key) 
TESTED _.property(key) 
TESTED _.matches(attrs) 
TESTED _.is_equal(object, other) 
TESTED _.is_empty(object) 
                                    * XXX NYI _.is_element(object) 
TESTED _.is_array(object) 
TESTED _.is_object(value) 
                                    * XXX NYI _.is_arguments(object) 
TESTED _.is_function(object) 
TESTED _.is_string(object) 
TESTED _.is_number(object) 
TESTED _.is_finite(object) 
TESTED _.is_boolean(object) 
                                    * XXX NYI _.is_date(object) 
                                    * XXX NYI _.is_regExp(object) 
TESTED _.is_nan(object) 
TESTED _.is_null(object) 
                                    * XXX NYI _.is_undefined(value) 


-- Utility Functions
-------------------------------------------------------------------------------
                                    * XXX NYI _.noConflict() 
TESTED _.identity(value) 
TESTED _.constant(value)
TESTED _.noop()
TESTED _.times(n, iteratee) 
TESTED _.random(min, max) 
                                    * XXX NYI _.mixin(object) 
                                    * XXX NYI _.iteratee(value, [context], [argCount]) 
                                    * XXX NYI _.uniqueId([prefix]) 
                                    * XXX NYI _.escape(string) 
                                    * XXX NYI _.unescape(string) 
                                    * XXX NYI _.result(object, property) 
TESTED _.now()
                                    * XXX NYI _.template(templateString, [settings]) 


-- Lua's Undercore Special Available Functions
-------------------------------------------------------------------------------
TESTED _.is_table (obj)
TESTED _.is_mixed(t)
TESTED _.is_integer (obj)
TESTED _.sort(t)
TESTED _.push (...)
TESTED _.pop (array)
TESTED _.concat (...)
TESTED _.split (str, re, plain, n)
TESTED _.is_uniq (array)
TESTED _.uniq_arg (...)
TESTED _.duplicates (...)
TESTED _.grep (str_predicate, iterable)
TESTED _.slice (array, start, ends)
]]--

-------------------------------------------------------------------------------
-------------------------------------------------------------------------------
return _
