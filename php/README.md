# How to install the CheckyCheck PHP Script on your Server

## Script arguments

Script usage:
```
php /root/checkycheck.php YOUR_USER_ID UPDATE_CYCLES
```

YOUR_USER_ID => Your Checkycheck user ID which you will find on your CheckyCheck Dashboard
UPDATE_CYCLES => How many cycles the script should do for updating the data Checkycheck receives (max. 30) (30 Updates with 2 Second Pause ~60 seconds)

If you set UPDATE_CYCLES to 30 Checkycheck will receive information from your Server every 2 seconds which allows you to use Checkycheck as a live dashboard!

## Automatic

```
curl https://raw.githubusercontent.com/userlip/checkycheck/main/php/checkycheck.php > /root/checkycheck.php && crontab -l | { cat; echo "* * * * * php /root/checkycheck.php YOUR_USER_ID 1"; } | crontab -
```

**Please do not forget to replace YOUR_USER_ID with your actual checkycheck user ID which you can find at: Checkycheck -> Servers -> Installation**
**The fully automated script with your user ID can also be found there**

## Manual

1. Download the PHP Script

```
curl https://raw.githubusercontent.com/userlip/checkycheck/main/php/checkycheck.php > /root/checkycheck.php
```

2. Install the cron

```
crontab -e
* * * * * php /root/checkycheck.php YOUR_USER_ID 1
```

**Please do not forget to replace YOUR_USER_ID with your actual checkycheck user ID which you can find at: Checkycheck -> Servers -> Installation**
**The fully automated script with your user ID can also be found there**
