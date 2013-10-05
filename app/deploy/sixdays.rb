set :domain,        "yourlife.sixdays.ru"
set :deploy_to,     "/var/www/#{domain}"

# deploy tasks
set :parameters_file, "parameters.yml.dist"

# dependencies
depend  :remote,    :writable,  deploy_to