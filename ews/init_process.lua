-- This code runs on the global Lua VM level when 
-- the nginx master process is loading the nginx config file
--
-- this file is called by init_by_lua_file on nginx's config file

-- globals on master's Lua VM

-- setup app paths
local app_path = '/opt/cinepixi/ews'

package.path = app_path .. '/?.lua;' ..
               app_path .. '/?/?.lua;' ..
               app_path .. '/lib/?.lua;' .. 
               app_path .. '/?/init.lua;' .. package.path

-- Globals loaded on master process
cjson = require 'cjson'
_     = require 'underscore'
log   = require 'tools.log'
tools = require 'tools'
time  = require 'lib.time'
