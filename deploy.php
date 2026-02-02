<?php
    chdir('/home/haley/deploy/cse135-site/home');
    
    // array of commands
    $commands = array(
        'echo $PWD',
        'whoami',
        'git pull origin main',
        'git status',
    );

    // exec commands
    $output = '';
    foreach($commands AS $command){
        $tmp = shell_exec($command);
        
        $output .= "<span style=\"color: #6BE234;\">\$</span><span style=\"color: #729FCF;\">{$command}\n</span><br />";
        $output .= htmlentities(trim($tmp)) . "\n<br /><br />";
    }
    
    // Run deployment script
    $deploy_output = shell_exec('sudo /usr/local/bin/deploy-cse135.sh 2>&1');
    $output .= "<span style=\"color: #6BE234;\">\$</span><span style=\"color: #729FCF;\">sudo /usr/local/bin/deploy-cse135.sh\n</span><br />";
    $output .= htmlentities(trim($deploy_output)) . "\n<br /><br />";

    // Rebuild C CGI files
    $build_output = shell_exec('cd /var/www/ah-nguyen.site/public_html/hw2/cgi-bin/c && ./build.sh 2>&1');
    $output .= "<span style=\"color: #6BE234;\">\$</span><span style=\"color: #729FCF;\">cd /var/www/ah-nguyen.site/public_html/hw2/cgi-bin/c && ./build.sh\n</span><br />";
    $output .= htmlentities(trim($build_output)) . "\n<br /><br />";

?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>GIT DEPLOYMENT SCRIPT</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>
 .  ____  .    ____________________________
 |/      \|   |                            |
[| <span style="color: #FF0000;">&hearts;    &hearts;</span> |]  | Git Deployment Script v0.1 |
 |___==___|  /              &copy; oodavid 2012 |
              |____________________________|

<?php echo $output; ?>
</pre>
</body>
</html>
