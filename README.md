# OGP Agent Linux

Open Game Panel (OGP) Agent for Linux - manages game servers remotely via XML-RPC.

## Quick Start

1. **Install dependencies**:
   ```bash
   sudo bash install_dependencies.sh
   ```

2. **Configure the agent**:
   ```bash
   cp Cfg/Config.pm.mysql_example Cfg/Config.pm
   nano Cfg/Config.pm  # Set your agent key at minimum
   ```

3. **Test configuration**:
   ```bash
   perl test_config.pl
   ```

4. **Start the agent**:
   ```bash
   perl ogp_agent.pl
   ```

## Documentation

- **[CONFIGURATION.md](CONFIGURATION.md)** - Detailed setup and configuration guide
- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Common issues and solutions

## System Requirements

- **OS**: Ubuntu 22.04 or higher (recommended)
- **Perl**: 5.20 or higher
- **Dependencies**: Run `install_dependencies.sh` to install required packages

## Key Features

- **Game Server Management**: Start, stop, restart game servers remotely
- **Resource Monitoring**: Automatic CPU, memory, and disk usage tracking  
- **MySQL Integration**: Optional database logging of system statistics
- **Scheduled Tasks**: Cron-like scheduler for automated maintenance
- **Steam Integration**: SteamCMD support for Steam-based games
- **Security**: Encrypted communication with web panel

## Configuration Files

- `Cfg/Config.pm` - Main configuration (create from example)
- `Cfg/Config.pm.mysql_example` - Example with MySQL enabled
- `Schedule/scheduler.tasks` - Custom scheduled tasks

## Testing Your Setup

Run the configuration test to verify everything is working:

```bash
perl test_config.pl
```

This will check:
- Configuration file validity
- Required settings
- Log file permissions
- Database connectivity (if configured)
- Required Perl modules

## Troubleshooting

### Agent won't start?
1. Run `perl test_config.pl` to diagnose issues
2. Check that `Cfg/Config.pm` exists and is configured
3. Verify the agent key matches your web panel
4. See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for detailed help

### No logging or MySQL connectivity?
- This was a common issue caused by missing `Cfg/Config.pm`
- The agent needs this file to know where to log and how to connect to MySQL
- Use `Cfg/Config.pm.mysql_example` as a starting point

### Empty scheduler.tasks file?
- The agent now creates a documented example file automatically
- See `Schedule/scheduler.tasks` for usage examples
- Resource statistics are scheduled automatically when database is configured

## Legacy Installation Script

The original installation script is available as `install_agent_prereqs.sh`.
