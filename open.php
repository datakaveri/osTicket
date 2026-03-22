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
        min-height: 58vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem 4rem;
    }
    .success-message-container {
        width: min(100%, 760px);
        margin: 0 auto;
        padding: 3rem 3.25rem;
        background:
            radial-gradient(circle at top left, rgba(236, 253, 245, 0.9), transparent 32%),
            linear-gradient(180deg, #ffffff 0%, #fcfffd 100%);
        border: 1px solid rgba(0, 208, 132, 0.12);
        border-radius: 28px;
        box-shadow:
            0 24px 60px rgba(15, 23, 42, 0.08),
            0 8px 24px rgba(0, 208, 132, 0.08);
        text-align: center;
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        animation: fadeInUp 0.6s ease-out;
        position: relative;
        overflow: hidden;
    }
    .success-message-container::after {
        content: "";
        position: absolute;
        inset: auto -60px -90px auto;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(0, 208, 132, 0.12) 0%, rgba(0, 208, 132, 0) 72%);
        pointer-events: none;
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
    .success-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        margin-bottom: 1.4rem;
    }
    .success-status-pill svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }
    .success-title {
        font-size: 2.75rem;
        font-weight: 700;
        color: #1a2e05;
        margin: 0 0 1rem 0;
        letter-spacing: -0.04em;
        line-height: 1.08;
    }
    .success-subtitle {
        max-width: 540px;
        margin: 0 auto 1.75rem;
        font-size: 1.08rem;
        color: #6b7280;
        line-height: 1.7;
    }
    .success-ticket-chip {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
        min-width: 250px;
        padding: 1rem 1.25rem;
        margin: 0 auto;
        border-radius: 20px;
        background: #f8fffb;
        border: 1px solid rgba(0, 208, 132, 0.18);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }
    .success-ticket-chip-label {
        font-size: 0.82rem;
        color: #6b7280;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .success-ticket-chip-value {
        font-size: 1.45rem;
        font-weight: 700;
        color: #047857;
        letter-spacing: -0.02em;
    }
    .success-actions {
        margin-top: 2.2rem;
        display: flex;
        gap: 1.25rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-success-action {
        background: #00d084;
        color: #0a0a0a;
        min-width: 230px;
        min-height: 56px;
        padding: 0.95rem 1.75rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.975rem;
        transition: all 150ms ease;
        box-shadow: 0 10px 24px rgba(0, 208, 132, 0.22);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        line-height: 1;
        letter-spacing: 0.01em;
        border: none;
    }
    .btn-success-action:hover {
        background: #00b371;
        box-shadow: 0 14px 28px rgba(0, 208, 132, 0.28);
        transform: translateY(-1px);
        color: #0a0a0a;
    }
    .btn-success-secondary {
        background: #ffffff;
        color: #00b371;
        min-width: 230px;
        min-height: 56px;
        padding: 0.95rem 1.75rem;
        border: 1.5px solid #00d084;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.975rem;
        transition: all 150ms ease;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        line-height: 1;
        letter-spacing: 0.01em;
    }
    .btn-success-secondary:hover {
        background: #f0fffa;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
        color: #00b371;
    }
    .btn-success-action svg,
    .btn-success-secondary svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }
    .success-helper {
        margin-top: 1.2rem;
        font-size: 0.95rem;
        color: #6b7280;
    }
    @media (max-width: 768px) {
        .success-message-container {
            padding: 2rem 1.35rem;
            border-radius: 22px;
        }
        .success-title { 
            font-size: 2rem; 
        }
        .success-subtitle {
            font-size: 1rem;
            margin-bottom: 1.35rem;
        }
        .success-ticket-chip {
            min-width: 0;
            width: 100%;
        }
        .success-actions {
            flex-direction: column;
            width: 100%;
        }
        .btn-success-action,
        .btn-success-secondary {
            width: 100%;
            min-width: 0;
            justify-content: center;
        }
    }
    </style>
    
    <div class="success-page-wrapper">
        <div class="success-message-container">
            <div class="success-status-pill">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Submission Confirmed
            </div>
            <h1 class="success-title">Ticket Created Successfully!</h1>
            <p class="success-subtitle">Thank you for reaching out. We've received your request and will get back to you shortly.</p>
            <div class="success-ticket-chip">
                <span class="success-ticket-chip-label">Reference Number</span>
                <span class="success-ticket-chip-value"><?php echo Format::htmlchars($ticket->getNumber()); ?></span>
            </div>
            
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
            <p class="success-helper">You can use this reference number to track updates on your request anytime.</p>
        </div>
    </div>
    <?php
}
else {
    require(CLIENTINC_DIR.'open.inc.php');
}
require(CLIENTINC_DIR.'footer.inc.php');
?>
