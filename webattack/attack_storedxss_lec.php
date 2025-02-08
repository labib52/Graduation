<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Understanding Stored XSS: An Overview</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        h1, h2 {
            color: #007BFF;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5rem;
            margin-top: 20px;
        }

        p {
            margin: 10px 0;
        }

        ul, ol {
            margin: 10px 0 10px 20px;
        }

        ul li, ol li {
            margin: 5px 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .nav-btn:hover {
            background-color: #0056b3;
        }

        .nav-btn span {
            margin: 0 5px;
        }

        .arrow {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .arrow-back {
            margin-right: 10px;
        }

        .arrow-next {
            margin-left: 10px;
        }

        code {
            background-color: #f4f4f9;
            padding: 5px;
            border-radius: 4px;
            font-family: monospace;
            display: block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Understanding Stored XSS: An Overview</h1>
        <p>
            Stored XSS, or Persistent XSS, is a web application security vulnerability that occurs when the application stores user-supplied input and later embeds it in web pages served to other users without proper sanitization or escaping. Examples include web forum posts, product reviews, user comments, and other data stores. In other words, stored XSS takes place when user input is saved in a data store and later included in the web pages served to other users without adequate escaping.
        </p>

        <h2>How Stored XSS Works</h2>
        <p>
            Stored XSS begins with an attacker injecting a malicious script into an input field of a vulnerable web application. The vulnerability might lie in how the web application processes data in comment boxes, forum posts, or profile information sections. When other users access this stored content, the injected malicious script executes within their browsers. The script can perform a wide range of actions, from stealing session cookies to performing actions on behalf of the user without their consent.
        </p>

        <h2>Vulnerable Web Applications</h2>
        <p>
            There are many reasons for a web application to be vulnerable to stored XSS. Below are some best practices to prevent stored XSS vulnerabilities:
        </p>
        <ul>
            <li><strong>Validate and sanitize input:</strong> Define clear rules and enforce strict validation on all user-supplied data. For instance, only alphanumeric characters can be used in a username, and only integers can be allowed in age fields.</li>
            <li><strong>Use output escaping:</strong> When displaying user-supplied input within an HTML context, encode all HTML-specific characters, such as &lt;, &gt;, and &amp;.</li>
            <li><strong>Apply context-specific encoding:</strong> For instance, within a JavaScript context, use JavaScript encoding whenever data is inserted into JavaScript code. Similarly, data placed in URLs must use relevant URL-encoding techniques, like percent-encoding.</li>
            <li><strong>Practice defense in depth:</strong> Don’t rely on a single layer of defense; use server-side validation instead of solely relying on client-side validation.</li>
        </ul>

        <h2>Examples of Vulnerable Code</h2>
        <h3>PHP</h3>
        <p><strong>Vulnerable Code:</strong></p>
        <code>
            // Vulnerable PHP code
            $comment = $_POST['comment'];
            // Save $comment to the database
            // Later, display comments without sanitization
            echo $comment;
        </code>
        <p><strong>Fixed Code:</strong></p>
        <code>
            // Fixed PHP code
            $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
            // Save $comment to the database
            // Display comments after sanitization
            echo $comment;
        </code>

        <h3>JavaScript (Node.js)</h3>
        <p><strong>Vulnerable Code:</strong></p>
        <code>
            // Vulnerable Node.js code
            const comment = comments[i];
            document.write(comment);
        </code>
        <p><strong>Fixed Code:</strong></p>
        <code>
            // Fixed Node.js code
            const comment = sanitizeHTML(comments[i]);
            document.write(comment);
        </code>

        <h3>Python (Flask)</h3>
        <p><strong>Vulnerable Code:</strong></p>
        <code>
            # Vulnerable Flask code
            comment_content = request.form['comment']
            # Save comment_content to the database
            # Display comments without escaping
            return render_template('comments.html', comment=comment_content)
        </code>
        <p><strong>Fixed Code:</strong></p>
        <code>
            # Fixed Flask code
            from flask import escape
            comment_content = escape(request.form['comment'])
            # Save comment_content to the database
            # Display comments after escaping
            return render_template('comments.html', comment=comment_content)
        </code>

        <h2>Conclusion</h2>
        <p>
            Stored XSS is a serious security vulnerability that can lead to data theft, unauthorized actions, and other malicious activities. By validating and sanitizing user input, using output escaping, and practicing defense in depth, developers can significantly reduce the risk of stored XSS attacks. Always ensure that user-supplied data is properly sanitized before being stored or displayed in web applications.
        </p>

        <div class="navigation">
            <a href="attack_storedxss.php" class="nav-btn">
                <span class="arrow arrow-back">&larr;</span>
                <span>Back</span>
            </a>
            <a href="attack_storedxss_lab.php" class="nav-btn">
                <span>Next</span>
                <span class="arrow arrow-next">&rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>
