# Open Game Panel Agent Configuration File
# This file contains the basic configuration settings for the OGP Agent
# Please edit the values below to match your environment
#
# IMPORTANT: You must configure at least the 'key' value for the agent to work properly

%Cfg::Config = (
	# Basic agent settings (REQUIRED)
	'key' => 'REPLACE_WITH_SECURE_KEY_FROM_WEB_PANEL',  # Must match the key in web panel
	'listen_ip' => '0.0.0.0',      # IP address to listen on (0.0.0.0 = all interfaces)
	'listen_port' => '12679',      # Port for XML-RPC communication
	'logfile' => '/tmp/ogp_agent.log',  # Path to log file (must be writable)
	'version' => '2.2.0',
	
	# Steam settings
	'steam_license' => 'Decline',  # Set to 'Accept' if you accept Steam's license
	'steam_dl_limit' => '0',       # Download limit in KB/s (0 = unlimited)
	
	# Web panel API settings (optional - for advanced integration)
	'web_admin_api_key' => '',     # API key from web panel admin
	'web_api_url' => '',           # URL to web panel API
	
	# Resource statistics database settings (optional - leave empty to disable)
	# If configured, the agent will automatically collect and submit system stats
	'stats_db_host' => '',         # MySQL host (e.g., 'localhost' or 'db.example.com')
	'stats_db_user' => '',         # MySQL username
	'stats_db_pass' => '',         # MySQL password
	'stats_db_name' => '',         # MySQL database name
	'stats_table_prefix' => 'ogp_',  # Prefix for database tables
	'stats_frequency_minutes' => '5',  # How often to collect stats (in minutes)
	
	# System settings
	'sudo_password' => '',         # Leave empty if agent user is in sudoers with NOPASSWD
);

# Return true to indicate successful loading
1;