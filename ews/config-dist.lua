local config = {}

-- MySQL config for localhost
config.db = {
  database = 'store',
  -- host     = '10.1.1.15',
  -- user     = 'mau',
  -- password = 'LlDW49uBvJ4eWBxncbAMHlxrVu4O5eV',
  host     = '10.1.1.18', -- web2
  user     = 'dani',
  password = '82378b5d9244f83d7ea55a74a33972a1',
  port     = 3306,
  max_packet_size = 16 * 1024 * 1024,
  timeout  = 60000
}

-- Log file
config.log = '/var/log/nginx/pws-error.log'

return config
