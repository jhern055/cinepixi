local cjson = require 'cjson'
local log   = require 'tools.log'

-- Globals Mapping
local ngx, null = ngx, ngx.null
local concat, insert   = table.concat, table.insert
local encode, decode   = cjson.encode, cjson.decode
local pcall, traceback = pcall, debug.traceback
local read_body, get_body_data = ngx.req.read_body, ngx.req.get_body_data
local get_uri_args, get_headers = ngx.req.get_uri_args, ngx.req.get_headers
local get_post_args = ngx.req.get_post_args

--- Parse request args
local function parse_request()
  local body, err = pcall(read_body)
  if not body then 
    log(ngx.WARN, {reason='read_body error: ' .. tostring(err), traceback=traceback()})
  end
  local method    = ngx.var.request_method
  local uri       = ngx.var.request_uri
  local body_data
  if body then
    body_data = get_body_data()
  end
  local args  = {}
  if body_data and body_data ~= '' then
    local ok, json = pcall(decode, body_data)
    if ok then args.json = json end
  end
  args.get  = get_uri_args(0)
  args.headers = get_headers()
  if not args.json and body then
    args.post = get_post_args(0)
  end
  return {
    method = method,
    uri    = uri,
    args   = args
  }
end

return parse_request
