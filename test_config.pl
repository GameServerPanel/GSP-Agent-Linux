#!/usr/bin/perl
# OGP Agent Configuration Test Script
# This script verifies that the agent configuration is valid and all required components are working

use strict;
use warnings;
use lib '.';

print "OGP Agent Configuration Test\n";
print "============================\n\n";

my $error_count = 0;

# Test 1: Check if Config.pm exists and loads
print "1. Testing configuration file...\n";
eval {
    require Cfg::Config;
    print "   ✓ Cfg/Config.pm loaded successfully\n";
};
if ($@) {
    print "   ✗ Failed to load Cfg/Config.pm: $@\n";
    $error_count++;
    exit(1);
}

# Test 2: Check required configuration values
print "\n2. Testing required configuration values...\n";
my @required_configs = (
    ['key', 'Agent key'],
    ['listen_ip', 'Listen IP'],
    ['listen_port', 'Listen port'],
    ['logfile', 'Log file path'],
    ['version', 'Agent version']
);

foreach my $config (@required_configs) {
    my ($key, $name) = @$config;
    if (defined $Cfg::Config{$key} && $Cfg::Config{$key} ne '') {
        print "   ✓ $name: $Cfg::Config{$key}\n";
    } else {
        print "   ✗ $name is not configured\n";
        $error_count++;
    }
}

# Test 3: Check if agent key is set to default
print "\n3. Testing agent key...\n";
if ($Cfg::Config{key} eq 'REPLACE_WITH_SECURE_KEY_FROM_WEB_PANEL' || 
    $Cfg::Config{key} eq 'REPLACE_WITH_SECURE_KEY') {
    print "   ⚠ Agent key is still set to default value - you need to configure it!\n";
    print "     Get the agent key from your OGP web panel and update Cfg/Config.pm\n";
    $error_count++;
} else {
    print "   ✓ Agent key is configured\n";
}

# Test 4: Check log file writability
print "\n4. Testing log file...\n";
my $logfile = $Cfg::Config{logfile};
eval {
    if (open(my $fh, '>>', $logfile)) {
        close($fh);
        print "   ✓ Log file is writable: $logfile\n";
    } else {
        print "   ✗ Cannot write to log file: $logfile ($!)\n";
        $error_count++;
    }
};
if ($@) {
    print "   ✗ Error testing log file: $@\n";
    $error_count++;
}

# Test 5: Check schedule directory and tasks file
print "\n5. Testing scheduler setup...\n";
if (-d 'Schedule') {
    print "   ✓ Schedule directory exists\n";
    if (-f 'Schedule/scheduler.tasks') {
        print "   ✓ scheduler.tasks file exists\n";
    } else {
        print "   ⚠ scheduler.tasks file missing (will be created automatically)\n";
    }
} else {
    print "   ⚠ Schedule directory missing (will be created automatically)\n";
}

# Test 6: Check database configuration (optional)
print "\n6. Testing database configuration (optional)...\n";
my $db_configured = 0;
if (defined($Cfg::Config{stats_db_host}) && $Cfg::Config{stats_db_host} ne '' &&
    defined($Cfg::Config{stats_db_user}) && $Cfg::Config{stats_db_user} ne '' &&
    defined($Cfg::Config{stats_db_pass}) && $Cfg::Config{stats_db_pass} ne '' &&
    defined($Cfg::Config{stats_db_name}) && $Cfg::Config{stats_db_name} ne '') {
    $db_configured = 1;
    
    # Try to test database connection
    eval {
        require DBI;
        my $dsn = "DBI:mysql:database=$Cfg::Config{stats_db_name};host=$Cfg::Config{stats_db_host}";
        my $dbh = DBI->connect($dsn, $Cfg::Config{stats_db_user}, $Cfg::Config{stats_db_pass}, {
            RaiseError => 0,
            PrintError => 0
        });
        if ($dbh) {
            print "   ✓ Database connection successful\n";
            print "   ✓ Resource statistics will be submitted to MySQL\n";
            $dbh->disconnect();
        } else {
            print "   ✗ Database connection failed: $DBI::errstr\n";
            print "   ⚠ Resource statistics will be logged but not submitted to MySQL\n";
        }
    };
    if ($@) {
        print "   ⚠ Cannot test database (DBI module not available): $@\n";
        print "   ⚠ Install libdbi-perl and libdbd-mysql-perl to enable database features\n";
    }
} else {
    print "   ⚠ Database not configured - resource statistics will be logged only\n";
    print "     This is optional. Configure database settings in Cfg/Config.pm to enable MySQL submission\n";
}

# Test 7: Check required Perl modules
print "\n7. Testing required Perl modules...\n";
my @required_modules = (
    'Fcntl',
    'File::Copy',
    'File::Basename',
    'MIME::Base64',
    'Getopt::Long',
    'Compress::Zlib'
);

foreach my $module (@required_modules) {
    eval "require $module";
    if ($@) {
        print "   ✗ Missing module: $module\n";
        $error_count++;
    } else {
        print "   ✓ $module available\n";
    }
}

# Summary
print "\n" . "="x50 . "\n";
print "CONFIGURATION TEST SUMMARY\n";
print "="x50 . "\n";

if ($error_count == 0) {
    print "✓ Configuration test PASSED!\n";
    print "✓ Your OGP Agent should start successfully.\n";
    print "\nNext steps:\n";
    print "1. Start the agent: perl ogp_agent.pl\n";
    print "2. Check the log file for startup messages\n";
    print "3. Verify the agent is listening on port $Cfg::Config{listen_port}\n";
} else {
    print "✗ Configuration test FAILED with $error_count error(s)!\n";
    print "✗ Please fix the errors above before starting the agent.\n";
    print "\nFor help, see:\n";
    print "- CONFIGURATION.md for setup instructions\n";
    print "- TROUBLESHOOTING.md for common issues\n";
}

exit($error_count > 0 ? 1 : 0);