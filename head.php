<head>
    <title><?php echo $json['name']; ?></title>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="<?php echo $json['keywords']; ?>">
    <meta name="description" content="<?php echo $json['description']; ?>">

    <script type="application/json+ld">
        <?php echo json_encode($json); ?>
    </script>

    <style type="text/css">
        body {
            margin: 40px auto;
            max-width: 60rem;
            line-height: 1.6;
            font-size: 18px;
            color: #444;
            padding: 0 10px;
        }

        h1, h2, h3 {
            line-height: 1.2;
        }

        h2, h3, h4 {
            margin-top: 2em;
            margin-bottom: 1em;
        }

        hr {
            margin: 4rem 0;
        }

        table td {
            padding-right: 2rem;
        }

        pre {
            overflow: auto;
            padding: 1rem;
            background-color: lightgrey;
        }

        code {
            font-size: 1rem;
        }

        var {
            color: dimgrey;
        }

        img {
            max-width: 100%;
        }

        header {
            display: flex;
        }

        header a {
            line-height: 3rem;
            display: block;
            position: relative;
            text-decoration: none;
        }
        
        header a[href="/"] {
            margin-right: auto;
            width: 3rem;
            background-image: url('/img/logo.svg');
            background-repeat: no-repeat;
            background-position: left center;
            background-size: contain;
        }
        
        header a:not(:first-child) {
            margin-left: 1rem;
        }

        aside {
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid lightgrey;
        }
    </style>

    <link rel="icon" type="image/svg" href="/img/logo.svg">
</head>
