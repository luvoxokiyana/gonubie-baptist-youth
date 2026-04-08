<?php
session_start();
$is_logged_in = isset($_SESSION['member_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Gonubie Baptist Youth</title>
    <link rel="stylesheet" href="css/main.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .faq-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .faq-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .faq-header h1 {
            color: #c9772e;
        }
        .faq-item {
            background: #fff;
            border-radius: 16px;
            margin-bottom: 1rem;
            border: 1px solid #e8e4d9;
            overflow: hidden;
        }
        .faq-question {
            padding: 1rem 1.5rem;
            background: #faf8f2;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-question:hover {
            background: #f0ede5;
        }
        .faq-question i {
            color: #c9772e;
            transition: transform 0.3s;
        }
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            color: #6b6a66;
            line-height: 1.6;
        }
        .faq-item.active .faq-answer {
            padding: 1rem 1.5rem;
            max-height: 500px;
        }
        .contact-box {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            margin-top: 2rem;
            border: 1px solid #e8e4d9;
        }
        .contact-box a {
            color: #c9772e;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="faq-container">
        <div class="faq-header">
            <h1><i class="fa-regular fa-circle-question"></i> Frequently Asked Questions</h1>
            <p>Everything you need to know about GBY</p>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                What is Gonubie Baptist Youth?
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                GBY is a youth group for grades 7-12 that meets every Friday at 7PM. We have Bible studies, games, worship nights, and community events.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                How do I get an account?
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                Contact one of our youth leaders to get your account set up. They'll create a username and password for you.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                How does voting work?
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                Every Friday, you can vote for the next week's Bible study topic and game. The winning choices appear on the home page and we use them for the following Friday.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                Why are gallery photos blurred?
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                For privacy and safety, only logged-in youth members can view photos. If you're a member, please login to see them clearly.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                Can I suggest a topic or game?
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                Absolutely! Use the suggestion box on the voting page to share your ideas. Leaders review suggestions for future polls.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                What if I forgot my password?
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                Contact a youth leader and they can reset your password for you.
            </div>
        </div>
        
        <div class="contact-box">
            <i class="fa-regular fa-envelope" style="font-size: 2rem; color: #c9772e;"></i>
            <h3>Still have questions?</h3>
            <p>Talk to any youth leader or email us at <a href="mailto:youth@gonubiebaptist.org.za">youth@gonubiebaptist.org.za</a></p>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        // Accordion functionality for FAQ
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                faqItem.classList.toggle('active');
            });
        });
    </script>
</body>
</html>