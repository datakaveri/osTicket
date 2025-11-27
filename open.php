<?php
/*********************************************************************
    open.php

    New tickets handle.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('client.inc.php');
define('SOURCE','Web'); //Ticket source.
$ticket = null;
$errors=array();
if ($_POST) {
    $vars = $_POST;
    $vars['deptId']=$vars['emailId']=0; //Just Making sure we don't accept crap...only topicId is expected.
    if ($thisclient) {
        $vars['uid']=$thisclient->getId();
    } elseif($cfg->isCaptchaEnabled()) {
        if(!$_POST['captcha'])
            $errors['captcha']=__('Enter text shown on the image');
        elseif(strcmp($_SESSION['captcha'], md5(strtoupper($_POST['captcha']))))
            $errors['captcha']=sprintf('%s - %s', __('Invalid'), __('Please try again!'));
    }

    $tform = TicketForm::objects()->one()->getForm($vars);
    $messageField = $tform->getField('message');
    $attachments = $messageField->getWidget()->getAttachments();
    if (!$errors) {
        $vars['message'] = $messageField->getClean();
        if ($messageField->isAttachmentsEnabled())
            $vars['files'] = $attachments->getFiles();
    }

    // Drop the draft.. If there are validation errors, the content
    // submitted will be displayed back to the user
    Draft::deleteForNamespace('ticket.client.'.substr(session_id(), -12));
    //Ticket::create...checks for errors..
    if(($ticket=Ticket::create($vars, $errors, SOURCE))){
        $msg=__('Support ticket request created');
        // Drop session-backed form data
        unset($_SESSION[':form-data']);
        //Logged in...simply view the newly created ticket.
        if ($thisclient && $thisclient->isValid()) {
            // Regenerate session id
            $thisclient->regenerateSession();
            @header('Location: tickets.php?id='.$ticket->getId());
        } else
            $ost->getCSRF()->rotate();
    }else{
        $errors['err'] = $errors['err'] ?: sprintf('%s %s',
            __('Unable to create a ticket.'),
            __('Correct any errors below and try again.'));
    }
}

//page
$nav->setActiveNav('new');
if ($cfg->isClientLoginRequired()) {
    if ($cfg->getClientRegistrationMode() == 'disabled') {
        Http::redirect('view.php');
    }
    elseif (!$thisclient) {
        require_once 'secure.inc.php';
    }
    elseif ($thisclient->isGuest()) {
        require_once 'login.php';
        exit();
    }
}

require(CLIENTINC_DIR.'header.inc.php');
if ($ticket
    && (
        (($topic = $ticket->getTopic()) && ($page = $topic->getPage()))
        || ($page = $cfg->getThankYouPage())
    )
) {
    // Thank the user and promise speedy resolution!
    // Hide the default message banner
    ?>
    <style>
    /* Hide default message banner */
    #msg_notice {
        display: none !important;
    }
    body {
        background: #f9fafb !important;
    }
    .success-page-wrapper {
        min-height: 50vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }
    .success-message-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 3rem;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .success-title {
        font-size: 36px;
        font-weight: 700;
        color: #1a2e05;
        margin: 0 0 1.5rem 0;
        letter-spacing: -0.5px;
        padding-top: 1rem;
    }
    .success-subtitle {
        font-size: 17px;
        color: #6b7280;
        margin: 0 0 3rem 0;
        line-height: 1.6;
    }
    .success-actions {
        margin-top: 2.5rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-success-action {
        background: #65a30d;
        color: #fff;
        padding: 14px 36px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(101, 163, 13, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-success-action:hover {
        background: #4d7c0a;
        box-shadow: 0 6px 20px rgba(101, 163, 13, 0.4);
        transform: translateY(-2px);
        color: #fff;
    }
    .btn-success-secondary {
        background: #fff;
        color: #65a30d;
        padding: 14px 36px;
        border: 2px solid #65a30d;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-success-secondary:hover {
        background: #f0fdf4;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-1px);
        color: #65a30d;
    }
    @media (max-width: 768px) {
        .success-message-container {
            padding: 2rem 1.5rem;
        }
        .success-title { 
            font-size: 28px; 
        }
        .success-subtitle {
            font-size: 16px;
        }
        .success-actions {
            flex-direction: column;
            width: 100%;
        }
        .btn-success-action,
        .btn-success-secondary {
            width: 100%;
            justify-content: center;
        }
    }
    </style>
    
    <div class="success-page-wrapper">
        <div class="success-message-container">
            <h1 class="success-title">Ticket Created Successfully!</h1>
            <p class="success-subtitle">Thank you for reaching out. We've received your request and will get back to you shortly.</p>
            
            <div class="success-actions">
                <a href="<?php echo ROOT_PATH; ?>tickets.php?id=<?php echo $ticket->getId(); ?>" class="btn-success-action">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" fill="currentColor"/>
                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    View My Ticket
                </a>
                <a href="<?php echo ROOT_PATH; ?>index.php" class="btn-success-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Go to Home
                </a>
            </div>
        </div>
    </div>
    <?php
}
else {
    require(CLIENTINC_DIR.'open.inc.php');
}
require(CLIENTINC_DIR.'footer.inc.php');
?>
