local app_path = '/var/www/html/exp/ews'
package.path = app_path .. '/?.lua;' ..
               app_path .. '/?/?.lua;' ..
               app_path .. '/lib/?.lua;' .. 
               app_path .. '/?/init.lua;' .. package.path

-- -- Globals loaded on master process
cjson = require '../cjson'
_     = require '../underscore'
log   = require '../tools.log'
tools = require '../tools'
time  = require '../lib.time'
-- init_by_lua_file '/var/www/html/exp/ews/init_process.lua';

local API = {}

function API.is_dir( sPath )
  if type( sPath ) ~= "string" then return false end

  local response = os.execute("cd ".. sPath)
  if response == 1 then
    return true
  end
  return  "cd ".. sPath
end

-- Lua implementation of PHP scandir function
function API.scandir(dirname)
        callit = os.tmpname()
        os.execute("ls -a1 "..dirname .. " >"..callit)
        f = io.open(callit,"r")
        rv = f:read("*all")
        f:close()
        os.remove(callit)

        tabby = {}
        local from  = 1
        local delim_from, delim_to = string.find( rv, "\n", from  )
        while delim_from do
                table.insert( tabby, string.sub( rv, from , delim_from-1 ) )
                from  = delim_to + 1
                delim_from, delim_to = string.find( rv, "\n", from  )
                end
        -- table.insert( tabby, string.sub( rv, from  ) )
        -- Comment out eliminates blank line on end!
        return tabby
end

-- ############################################################### 

function API.get_Paths(path)
  local result = {}
  local exclude = {".",".."}

  filetab = API.scandir(path)

  _.each(filetab,function (filetab_row)

    if not _.contains(exclude,filetab_row) then

      if(API.is_dir(path.."/"..filetab_row) )then

      result[filetab_row]=API.get_Paths(path.."/"..filetab_row)

      else

 --  ngx.say(cjson.encode(filetab))
--   ngx.say(filetab_row)
      result[filetab_row]=filetab_row

      end

    end

  end)

return result;
end

function API.syn_Paths(params)
  local art_dir ="/media/dell/67F18E800D673AB3/musica"

  filetab = API.get_Paths(art_dir)

   ngx.say(cjson.encode(filetab))
  return ngx.exit(ngx.HTTP_OK)  
end
API.syn_Paths(params)
return API
