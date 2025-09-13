# Resource Monitoring Configuration

The OGP Agent now includes comprehensive resource monitoring that collects system statistics and submits them to a MySQL database.

## Features

The agent collects the following resource statistics:
- CPU usage percentage (average across all cores)
- Memory usage (used, total, percentage in bytes)
- Disk usage (used, total, free, percentage in bytes)
- System uptime (seconds)
- Load averages (1min, 5min, 15min)

## Configuration

Resource monitoring is configured during agent setup via the `agent_conf.sh` script. The following database settings are required:

- **Stats DB Host**: MySQL server hostname (default: 127.0.0.1)
- **Stats DB User**: MySQL username (default: panel_user)
- **Stats DB Password**: MySQL password (default: REPLACE_ME)
- **Stats DB Name**: MySQL database name (default: panel_database)
- **Stats Table Prefix**: Table prefix for resource stats (default: gsp_)
- **Stats Frequency Minutes**: Collection frequency in minutes (default: 5)

## Database Schema

The agent automatically creates the following table:

```sql
CREATE TABLE `{prefix}agent_resource_stats` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `agent_key` VARCHAR(255) NOT NULL,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `cpu_usage_percent` DECIMAL(5,2) NOT NULL,
    `memory_used_bytes` BIGINT NOT NULL,
    `memory_total_bytes` BIGINT NOT NULL,
    `memory_usage_percent` DECIMAL(5,2) NOT NULL,
    `disk_used_bytes` BIGINT NOT NULL,
    `disk_total_bytes` BIGINT NOT NULL,
    `disk_free_bytes` BIGINT NOT NULL,
    `disk_usage_percent` DECIMAL(5,2) NOT NULL,
    `uptime_seconds` INT NOT NULL,
    `load_average_1min` DECIMAL(4,2) NOT NULL,
    `load_average_5min` DECIMAL(4,2) NOT NULL,
    `load_average_15min` DECIMAL(4,2) NOT NULL,
    INDEX `idx_agent_key` (`agent_key`),
    INDEX `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Dependencies

The resource monitoring functionality requires the following Perl modules:
- `DBI` - Database Interface
- `DBD::mysql` - MySQL driver for DBI

Install these dependencies using:
```bash
# On Debian/Ubuntu
sudo apt-get install libdbi-perl libdbd-mysql-perl

# On CentOS/RHEL
sudo yum install perl-DBI perl-DBD-MySQL

# Or using CPAN
cpan install DBI DBD::mysql
```

## Logging

Resource collection activities are logged to the agent log file with the following types of messages:

- `"Collecting resource usage statistics..."` - When manual collection starts
- `"Scheduled resource stats collection started."` - When automated collection starts
- `"Resource stats collected - CPU: X%, Memory: Y%..."` - Detailed stats summary
- `"Resource statistics successfully submitted to MySQL database."` - Successful submission
- `"Resource stats database not configured - skipping database submission."` - When DB not configured
- `"Failed to submit resource statistics to MySQL database - error occurred."` - On errors

## Manual Testing

The resource collection can be triggered manually via the `mon_stats` RPC function, which will:
1. Collect all resource statistics
2. Submit them to the database (if configured)
3. Log the collection and submission results
4. Return compatibility data for existing clients

## Scheduled Collection

When the agent starts, it automatically schedules resource collection based on the `stats_frequency_minutes` configuration. The collection runs as a background cron job and operates independently of manual requests.

## Troubleshooting

1. **Database connection failures**: Check MySQL server accessibility, credentials, and firewall settings
2. **Missing dependencies**: Install required Perl modules (DBI, DBD::mysql)
3. **Permission issues**: Ensure the agent user has appropriate database permissions
4. **Frequency not working**: Verify `stats_frequency_minutes` is a positive integer in the configuration