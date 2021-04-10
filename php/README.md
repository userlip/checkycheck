# How to install the CheckyCheck PHP Script on your Server

## Automatic

```
curl ... > /root/checkycheck.php && crontab -l | { cat; echo "* * * * * php /root/checkycheck.php YOUR_USER_ID"; } | crontab -
```

**Please do not forget to replace YOUR_USER_ID with your actual checkycheck user ID which you can find at: Checkycheck -> Servers -> Installation**
**The fully automated script with your user ID can also be found there**

## Manual

1. Download the PHP Script

```
curl ... > /root/checkycheck.php
```

2. Install the cron

```
crontab -e
* * * * * php /root/checkycheck.php YOUR_USER_ID
```

**Please do not forget to replace YOUR_USER_ID with your actual checkycheck user ID which you can find at: Checkycheck -> Servers -> Installation**
**The fully automated script with your user ID can also be found there**
