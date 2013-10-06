set :stages,        %w(sixdays)
set :stage_dir,     "app/deploy"

require 'capistrano/ext/multistage'

set :application,           "yourlife app"
set :scm,                   :git
set :repository,            "."
set :deploy_via,            :capifony_copy_local
set :use_composer,          true
set :composer_options,      "--no-dev --verbose --prefer-dist --optimize-autoloader --no-progress"
set :use_composer_tmp,      true
set :interactive_mode,      false
set :dump_assetic_assets,   false
set :use_sudo,              false
set :keep_releases,         5
set :user,                  "sixdays"
set :group_writable,        false
set :shared_files,          []
set :shared_children,       [app_path + "/logs", app_path + "/../web/uploads"]

ssh_options[:forward_agent] = true
logger.level                = Logger::MAX_LEVEL

##############
# deploy tasks
##############

set :parameters_dir,        "app/config"
set :parameters_file,       false

after :deploy, "deploy:upload_parameters"
after :deploy, "deploy:cleanup"

namespace :deploy do

    desc 'upload parameters.yml'
    task :upload_parameters do
        origin_file = parameters_dir + "/" + parameters_file if parameters_dir && parameters_file
        if origin_file && File.exists?(origin_file)
            relative_path = "app/config/parameters.yml"

            if shared_files && shared_files.include?(relative_path)
                destination_file = shared_path + "/" + relative_path
            else
                destination_file = latest_release + "/" + relative_path
            end
            try_sudo "mkdir -p #{File.dirname(destination_file)}"

            top.upload(origin_file, destination_file)
        end
    end
end