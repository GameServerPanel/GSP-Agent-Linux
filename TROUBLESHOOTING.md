# OGP Agent Troubleshooting Guide

## Common Issues and Solutions

### 1. Agent Won't Start - "Can't locate Cfg/Config.pm"

**Problem**: The configuration file is missing.

**Solution**: 
```bash
# Create the configuration file
cp Cfg/Config.pm.mysql_example Cfg/Config.pm
# Edit the configuration file
nano Cfg/Config.pm
# Set at minimum the 'key' value
```

### 2. No Logging - Empty or Missing Log Files

**Problem**: Nothing is being logged to the log file.

**Possible Causes & Solutions**:

1. **Missing Config.pm**: Follow solution #1 above
2. **Wrong log file path**: Check `logfile` setting in `Cfg/Config.pm`
3. **Permission issues**: Ensure the agent user can write to the log directory
   ```bash
   # Check permissions
   ls -la /tmp/ogp_agent.log
   # Fix permissions if needed
   sudo chown $(whoami) /tmp/ogp_agent.log
   ```

### 3. MySQL Connection Errors - Stats Not Submitting

**Problem**: Resource statistics are not being sent to MySQL.

**Debugging Steps**:

1. **Check if database is configured**:
   ```bash
   # Run this test
   cd /path/to/agent
   perl -e "
   use lib '.';
   use Cfg::Config;
   print 'DB Host: ' . \$Cfg::Config{stats_db_host} . \"\n\";
   print 'DB User: ' . \$Cfg::Config{stats_db_user} . \"\n\";
   print 'DB Name: ' . \$Cfg::Config{stats_db_name} . \"\n\";
   "
   ```

2. **Test MySQL connectivity manually**:
   ```bash
   mysql -h your_host -u your_user -p your_database
   ```

3. **Check database permissions**:
   ```sql
   GRANT CREATE, INSERT, SELECT ON ogp_database.* TO 'ogp_stats'@'localhost';
   FLUSH PRIVILEGES;
   ```

4. **Common error messages**:
   - `"Resource stats database not configured"` - Database settings are empty
   - `"Failed to connect to MySQL database"` - Check credentials and host
   - `"Access denied"` - Check MySQL user permissions

### 4. Scheduler Not Working - Empty scheduler.tasks File

**Problem**: Scheduled tasks are not executing.

**Debugging Steps**:

1. **Check scheduler.tasks file exists**:
   ```bash
   ls -la Schedule/scheduler.tasks
   ```

2. **Check scheduler log**:
   ```bash
   tail -f Schedule/scheduler.log
   ```

3. **Verify task syntax**:
   ```bash
   # Valid task format:
   # minute hour day month dayofweek command
   0 2 * * * echo "Test task"
   ```

4. **Check scheduler process**:
   ```bash
   ps aux | grep scheduler
   cat Schedule/scheduler.pid
   ```

### 5. Permission Denied Errors

**Problem**: Various permission denied errors when running the agent.

**Solutions**:

1. **Don't run as root**: The agent specifically prevents running as root
2. **Add user to sudoers** (if needed):
   ```bash
   sudo visudo
   # Add line:
   ogp_user ALL=(ALL) NOPASSWD: ALL
   ```
3. **Fix file permissions**:
   ```bash
   chmod 755 ogp_agent.pl
   chmod 644 Cfg/Config.pm
   chmod 755 Schedule/
   ```

### 6. Steam License Issues

**Problem**: Steam-related functionality not working.

**Solution**: Accept the Steam license in `Cfg/Config.pm`:
```perl
'steam_license' => 'Accept',
```

### 7. Missing Perl Modules

**Problem**: "Can't locate [module].pm" errors.

**Solution**: Install missing dependencies:
```bash
# Run the dependency installer
sudo bash install_dependencies.sh

# Or install specific modules:
sudo apt-get install libdbi-perl libdbd-mysql-perl
```

## Log File Locations

Check these files for diagnostic information:

- Main agent log: `/tmp/ogp_agent.log` (or path in Config.pm)
- Scheduler log: `Schedule/scheduler.log`
- System log: `/var/log/syslog` (may contain agent startup errors)

## Diagnostic Commands

### Test Configuration Loading
```bash
cd /path/to/agent
perl -e "use lib '.'; use Cfg::Config; print 'Config OK\n';"
```

### Test Database Connection
```bash
cd /path/to/agent
perl -e "
use lib '.';
use Cfg::Config;
use DBI;
my \$dsn = 'DBI:mysql:database=' . \$Cfg::Config{stats_db_name} . ';host=' . \$Cfg::Config{stats_db_host};
my \$dbh = DBI->connect(\$dsn, \$Cfg::Config{stats_db_user}, \$Cfg::Config{stats_db_pass});
print \$dbh ? 'Database connection OK\n' : 'Database connection failed\n';
"
```

### Check Agent Syntax
```bash
cd /path/to/agent
perl -c ogp_agent.pl
```

## Getting Help

If you're still experiencing issues:

1. Check the main agent log file for specific error messages
2. Run the agent with verbose logging: `perl ogp_agent.pl --log-stdout`
3. Verify all configuration settings in `Cfg/Config.pm`
4. Test individual components (config loading, database connection, etc.)
5. Check system requirements and dependency installation