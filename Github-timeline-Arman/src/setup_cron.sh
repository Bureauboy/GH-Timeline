#!/bin/bash
# Add a CRON job to run cron.php every 5 minutes

CRON_CMD="*/5 * * * * php $(pwd)/cron.php"
# Remove any previous instance of this job
(crontab -l 2>/dev/null | grep -v 'cron.php'; echo "$CRON_CMD") | crontab -
echo "CRON job added: $CRON_CMD"
