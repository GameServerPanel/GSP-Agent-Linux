# OGP Agent Configuration Guide

## Initial Setup

After installing the OGP Agent, you must configure it before it can run properly.

### 1. Basic Configuration (Required)

Edit `Cfg/Config.pm` and set at minimum:

```perl
'key' => 'your_agent_key_from_web_panel',
```

This key must match the agent key configured in your OGP web panel.

### 2. Log File Configuration

The default log file location is `/tmp/ogp_agent.log`. For production use, consider changing this to:

```perl
'logfile' => '/var/log/ogp_agent.log',
```

Make sure the agent user has write permissions to the log file location.

### 3. Resource Statistics (Optional)

To enable automatic resource statistics collection and MySQL submission:

1. Create a MySQL database and user for statistics
2. Configure the database settings in `Cfg/Config.pm`:

```perl
'stats_db_host' => 'localhost',
'stats_db_user' => 'ogp_stats',
'stats_db_pass' => 'your_password',
'stats_db_name' => 'ogp_database',
'stats_frequency_minutes' => '5',
```

The agent will automatically:
- Create the necessary database table
- Collect CPU, memory, disk usage, and system load
- Submit statistics every 5 minutes (configurable)

### 4. Scheduler Tasks

The scheduler.tasks file in the `Schedule/` directory can contain custom scheduled tasks:

```
# Run maintenance every day at 2 AM
0 2 * * * /path/to/maintenance_script.sh

# Check disk space every hour
0 * * * * df -h > /tmp/disk_usage.log
```

### 5. Steam Integration

If you plan to run Steam-based game servers, accept the Steam license:

```perl
'steam_license' => 'Accept',
```

## Troubleshooting

### Agent Won't Start
- Check that `Cfg/Config.pm` exists and has valid syntax
- Verify the log file path is writable
- Make sure all required Perl modules are installed

### No Logging
- Verify the log file path in `Cfg/Config.pm`
- Check file permissions on the log directory
- Ensure the agent key is configured

### MySQL Connection Issues
- Verify database credentials in `Cfg/Config.pm`
- Test database connectivity manually
- Check MySQL user permissions
- Review agent logs for specific error messages

### Resource Stats Not Submitting
- Ensure database configuration is complete
- Check that `stats_frequency_minutes` is a positive number
- Verify MySQL user has CREATE and INSERT permissions
- Look for database errors in the scheduler log file

## Files Created

The agent will automatically create:
- `Schedule/scheduler.tasks` (if it doesn't exist)
- `Schedule/scheduler.pid` (when scheduler is running)
- `Schedule/scheduler.log` (scheduler activity log)
- Database table (if MySQL is configured)