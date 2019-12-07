<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BeeJee Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <?php foreach ($styles as $item): ?>
        <link rel="stylesheet" href="<?php echo $item; ?>" />
    <?php endforeach; ?>
</head>
    <body class="pb-5">
        <nav class="navbar navbar-expand-lg bg-light mb-5">
            <a class="navbar-brand" href="<?php echo $url->get('/'); ?>">BeeJee TODO</a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="my-2 my-lg-0 ml-auto">
                    <?php
                        $headerBlock = new \BeeJeeTest\Core\View('Auth/Login/HeaderBlock');
                        $headerBlock->set('user', $user);
                        $headerBlock->set('url', $url);
                        echo $headerBlock->render();
                    ?>
                </div>
            </div>
        </nav>
        <?php echo $content; ?>
    </body>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <?php foreach ($scripts as $item): ?>
        <script type="text/javascript" src="<?php echo $item; ?>"></script>
    <?php endforeach; ?>
</html>