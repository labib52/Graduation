<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Understanding Reflected XSS: An Overview</title>
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
        <h1>Understanding Reflected XSS: An Overview</h1>
        <p>
            Reflected XSS is a type of XSS vulnerability where a malicious script is reflected to the user’s browser, often via a crafted URL or form submission. Consider a search query containing <code>&lt;script&gt;alert(document.cookie)&lt;/script&gt;</code> many users wouldn’t be suspicious about such a URL, even if they look at it up close. If processed by a vulnerable web application, it will be executed within the context of the user’s browser.
        </p>

        <h2>How Reflected XSS Works</h2>
        <p>
            In this innocuous example, it displays the cookie in an alert box. However, an attacker wants to achieve more than just displaying the cookie as an alert to the user. For such an attack to be possible, we need a vulnerable application.
        </p>

        <h2>Vulnerable Web Application</h2>
        <p>
            One simple reflected XSS vulnerability occurs when the user searches for some term, and the search string is included verbatim in the results page. This simple scenario provides an easy target for the attacker to exploit.
        </p>
        <p>
            Although discovering such vulnerabilities is not always easy, fixing them is straightforward. User input such as <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> should be sanitized or HTML-encoded to <code>&amp;lt;script&amp;gt;alert('XSS')&amp;lt;/script&amp;gt;</code>.
        </p>

        <h2>Examples of Vulnerable Code</h2>
        <h3>PHP</h3>
        <p><strong>Vulnerable Code:</strong></p>
        <code>
            // Vulnerable PHP code
            $search = $_GET['q'];
            echo "You searched for: " . $search;
        </code>
        <p>If you are unfamiliar with PHP, $_GET is a PHP array containing values from the URL query string. Furthermore, $_GET['q'] refers to the query string parameter q. For example, in http://shop.thm/search.php?q=table, $_GET['q'] has the value table.
As you might have guessed, the vulnerability is caused by the search value displayed on the result page without sanitization. Therefore, an attacker can add a malicious script to the URL, knowing it would be executed. For example, as a proof of concept, the following URL can be tested: http://shop.thm/search.php?q=alert(document.cookie) and if the site is vulnerable, an alert box will appear displaying the user’s cookie.
 </p>
        <p><strong>Fixed Code:</strong></p>
        <code>
            // Fixed PHP code
            $search = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
            echo "You searched for: " . $search;
        </code>
        <p>
            The PHP function htmlspecialchars() converts special characters to HTML entities. The characters <, >, &, ", ' are replaced by default to prevent scripts in the input from executing.
        </p>
        <h3>JavaScript (Node.js)</h3>
        <p><strong>Vulnerable Code:</strong></p>
        <code>
            // Vulnerable Node.js code
            const search = req.query.q;
            res.send("You searched for: " + search);
        </code>
        <p>
        If you are unfamiliar with Node.js, the code snippet above uses Express, a popular web application framework for Node.js. The req.query.q will extract the value of q. For example, in http://shop.thm/search?q=table, req.query.q has the value table. Finally, the response is generated by appending the search term provided by the user to “You searched for:”.
Because the value is taken from the user and inserted in the response HTML without sanitization or escaping, it is easy to append a malicious query. As a proof of concept, we can test the following URL: http://shop.thm/search?q= alert(document.cookie), and if the site is vulnerable, an alert box will appear displaying the user’s cookie.
        </p>
        <p><strong>Fixed Code:</strong></p>
        <code>
            // Fixed Node.js code
            const search = sanitizeHtml(req.query.q);
            res.send("You searched for: " + search);
        </code>
        <p>
        The solution is achieved by using the sanitizeHtml() from the sanitize-html library. This function removes unsafe elements and attributes. This includes removing script tags, among other elements that could be used for malicious purposes.
Another approach would be by using the escapeHtml() function instead of the sanitizeHtml() function. As the name indicates, the escapeHtml() function aims to escape characters such as <, >, &, ", and '.
</p>

        <h3>Python (Flask)</h3>
        <p><strong>Vulnerable Code:</strong></p>
        <code>
            # Vulnerable Flask code
            search = request.args.get("q")
            return f"You searched for: {search}"
        </code>
        <p>
        If you are unfamiliar with Flask, request.args.get() is used to access query string parameters from the request URL. In fact, request.args contains all the query string parameters in a dictionary-like object. For example, in http://shop.thm/search?q=table, request.args.get("q") has the value table.
Because the value is taken from the user and inserted in the response HTML without sanitization or escaping, it is easy to append a malicious query. As a proof of concept, we can test the following URL: http://shop.thm/search?q= alert(document.cookie)and if the site is vulnerable, an alert box will appear displaying the user’s cookie.
        </p>
        <p><strong>Fixed Code:</strong></p>
        <code>
            # Fixed Flask code
            from markupsafe import escape
            search = escape(request.args.get("q"))
            return f"You searched for: {search}"
        </code>
        <p>
        The main change is that the user input is now escaped using the escape() function from the html module. Note that the html.escape() function in Flask is actually an alias for markupsafe.escape(). They both come from the Werkzeug library and serve the same purpose: escaping unsafe characters in strings. This function converts characters like <, >, ", ' to HTML escaped entities, disarming any malicious code the user has inserted
        </p>

        <h2>Conclusion</h2>
        <p>
            Reflected XSS is a serious security vulnerability that can lead to data theft, unauthorized actions, and other malicious activities. By validating and sanitizing user input, using output escaping, and practicing defense in depth, developers can significantly reduce the risk of reflected XSS attacks. Always ensure that user-supplied data is properly sanitized before being displayed in web applications.
        </p>

        <div class="navigation">
            <a href="attack_reflectedxss.php" class="nav-btn">
                <span class="arrow arrow-back">&larr;</span>
                <span>Back</span>
            </a>
            <a href="attack_reflectedxss_lab.php" class="nav-btn">
                <span>Next</span>
                <span class="arrow arrow-next">&rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>
