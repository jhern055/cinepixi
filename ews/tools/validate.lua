local error = error
local check = tools.check
local API = {}


-- This function allows to look over all the params incoming from the UI
-- instead of do ifs for validate every fields, the VALIDATIONS table its define
-- in the top of the file.

--- Validate_grid  (table, node, table)
--
-- Gets a json incoming from the ui with all the new values for the node,
-- a node from gdb and a table with all the validations from the type 
--fileds ex. {id = 'number', name = 'string',  sort_order = 'number'}
-- then take the json and compare the type of the data with the specific 
-- name field in validations table, and if the type of the json value its equals
-- to the value of the key with the same name, then save it.

function API.validate_grid( json, node, validations )
  _.each(json, function (v, k)
    if v and check(v, validations[k]) then
      node.data[k] = v
    end
  end)
  return node
end

return API
