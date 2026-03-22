<?php
if (!defined('OSTCLIENTINC') || !$thisclient || !$ticket || !$ticket->checkUserAccess($thisclient)) die('Access Denied!');

require_once INCLUDE_DIR . 'client/mahaagx-keycloak-url.inc.php';

$info = ($_POST && $errors) ? Format::htmlchars($_POST) : array();

$type = array('type' => 'viewed');
Signal::send('object.view', $ticket, $type);

$dept = $ticket->getDept();

if ($ticket->isClosed() && !$ticket->isReopenable())
    $warn = sprintf(__('%s is marked as closed and cannot be reopened.'), __('This ticket'));

//Making sure we don't leak out internal dept names
if (!$dept || !$dept->isPublic())
    $dept = $cfg->getDefaultDept();

if (
    $thisclient && $thisclient->isGuest()
    && $cfg->isClientRegistrationEnabled()
) { ?>
    <div id="msg_info">
        <i class="icon-compass icon-2x pull-left"></i>
        <strong><?php echo __('Looking for your other tickets?'); ?></strong><br />
        <a href="<?php echo ROOT_PATH; ?>login.php?e=<?php
                                                        echo urlencode($thisclient->getEmail());
                                                        ?>" style="text-decoration:underline"><?php echo __('Sign In'); ?></a>
        <?php echo sprintf(
            __('or %s register for an account %s for the best experience on our help desk.'),
            '<a href="' . Format::htmlchars(MAHAAGX_KEYCLOAK_REGISTER_URL) . '" style="text-decoration:underline">',
            '</a>'
        ); ?>
    </div>
<?php } ?>

<style>
    .maha-ticket-page {
        width: min(100%, 1200px);
        margin: 0 auto 3rem;
        padding: 0.5rem 0 2rem;
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #1f2937;
    }

    .maha-ticket-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5rem;
        padding: 1.75rem 2rem;
        margin-bottom: 1.25rem;
        background:
            radial-gradient(circle at top left, rgba(236, 253, 245, 0.9), transparent 32%),
            linear-gradient(180deg, #ffffff 0%, #fcfffd 100%);
        border: 1px solid rgba(0, 208, 132, 0.12);
        border-radius: 24px;
        box-shadow:
            0 18px 40px rgba(15, 23, 42, 0.06),
            0 8px 24px rgba(0, 208, 132, 0.06);
    }

    .maha-ticket-title-wrap {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        min-width: 0;
    }

    .maha-ticket-refresh {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #ecfdf5;
        color: #047857;
        text-decoration: none;
        flex-shrink: 0;
        transition: 150ms ease;
    }

    .maha-ticket-refresh:hover {
        background: #d1fae5;
        color: #059669;
        transform: translateY(-1px);
    }

    .maha-ticket-eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 0.85rem;
    }

    .maha-ticket-title {
        margin: 0;
        font-family: 'Caladea', Georgia, serif;
        font-size: 2rem;
        line-height: 1.08;
        color: #1a2e05;
        letter-spacing: -0.03em;
        word-break: break-word;
    }

    .maha-ticket-number {
        display: inline-block;
        margin-top: 0.6rem;
        font-size: 0.98rem;
        font-weight: 700;
        color: #047857;
    }

    .maha-ticket-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .maha-ticket-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .maha-ticket-card {
        background: #ffffff;
        border: 1px solid #e5f7ef;
        border-radius: 20px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .maha-ticket-card__head {
        padding: 1rem 1.25rem;
        background: #f8fffb;
        border-bottom: 1px solid #dff7eb;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a2e05;
    }

    .maha-ticket-meta {
        display: grid;
        grid-template-columns: max-content 1fr;
        gap: 0.9rem 1rem;
        padding: 1.1rem 1.25rem 1.2rem;
        margin: 0;
    }

    .maha-ticket-meta dt {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 700;
        color: #6b7280;
    }

    .maha-ticket-meta dd {
        margin: 0;
        font-size: 0.95rem;
        color: #111827;
    }

    .maha-ticket-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .maha-custom-grid {
        display: grid;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .maha-custom-grid .custom-data {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        border: 1px solid #e5f7ef;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .maha-custom-grid .custom-data .headline {
        padding: 1rem 1.25rem;
        background: #f8fffb;
        border-bottom: 1px solid #dff7eb;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a2e05;
    }

    .maha-custom-grid .custom-data th,
    .maha-custom-grid .custom-data td {
        padding: 0.95rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        text-align: left;
    }

    .maha-custom-grid .custom-data th {
        width: 220px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #6b7280;
    }

    .maha-custom-grid .custom-data tr:last-child th,
    .maha-custom-grid .custom-data tr:last-child td {
        border-bottom: none;
    }

    .maha-thread-card,
    .maha-reply-card {
        background: #ffffff;
        border: 1px solid #e5f7ef;
        border-radius: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .maha-thread-card {
        margin-bottom: 1.25rem;
        padding: 1.25rem;
    }

    #ticketThread .thread-entry {
        margin-bottom: 1rem;
        border: 1px solid #dff7eb;
        border-radius: 18px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    #ticketThread .thread-entry:last-child {
        margin-bottom: 0;
    }

    #ticketThread .thread-entry .header {
        padding: 0.95rem 1.1rem;
        background: #f8fffb;
        border-bottom: 1px solid #dff7eb;
        color: #374151;
        font-size: 0.9rem;
    }

    #ticketThread .thread-entry .thread-body {
        padding: 1rem 1.1rem 1.1rem;
        color: #111827;
        line-height: 1.7;
    }

    #ticketThread .thread-entry .attachments {
        margin-top: 0.85rem;
        padding-top: 0.75rem;
        border-top: 1px dashed #d1fae5;
    }

    .maha-reply-card {
        padding: 1.5rem;
    }

    .maha-reply-card h2 {
        margin: 0 0 0.65rem;
        font-family: 'Caladea', Georgia, serif;
        font-size: 1.7rem;
        color: #1a2e05;
    }

    .maha-reply-card .reply-help {
        margin: 0 0 1rem;
        color: #6b7280;
        line-height: 1.6;
    }

    .maha-reply-card textarea {
        border-radius: 18px !important;
        border: 1px solid #d1fae5 !important;
        background: #fcfffd !important;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
    }

    .maha-reply-card textarea:focus {
        border-color: #00d084 !important;
        box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.12) !important;
    }

    .maha-reply-card .warning-banner {
        margin-top: 1rem;
        padding: 0.9rem 1rem;
        border-radius: 14px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #c2410c;
        font-weight: 600;
    }

    @media screen and (max-width: 900px) {
        .maha-ticket-hero {
            padding: 1.35rem;
            flex-direction: column;
        }

        .maha-ticket-info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media screen and (max-width: 600px) {
        .maha-ticket-page {
            padding-bottom: 1rem;
        }

        .maha-ticket-title {
            font-size: 1.6rem;
        }

        .maha-ticket-card__head,
        .maha-custom-grid .custom-data .headline,
        .maha-custom-grid .custom-data th,
        .maha-custom-grid .custom-data td,
        .maha-ticket-meta {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .maha-ticket-meta {
            grid-template-columns: 1fr;
            gap: 0.25rem;
        }

        .maha-custom-grid .custom-data th {
            width: auto;
            padding-bottom: 0.2rem;
            border-bottom: none;
        }

        .maha-custom-grid .custom-data td {
            padding-top: 0;
        }
    }
</style>

<div class="maha-ticket-page">
    <?php
    $subject_field = TicketForm::getInstance()->getField('subject');
    $ticketSubject = $subject_field->display($ticket->getSubject());
    $ticketStatus = ($S = $ticket->getStatus()) ? $S->getLocalName() : '';
    ?>
    <div class="maha-ticket-hero">
        <div class="maha-ticket-title-wrap">
            <a class="maha-ticket-refresh" href="tickets.php?id=<?php echo $ticket->getId(); ?>" title="<?php echo __('Reload'); ?>">
                <i class="refresh icon-refresh"></i>
            </a>
            <div>
                <div class="maha-ticket-eyebrow"><?php echo __('Support Ticket'); ?></div>
                <h1 class="maha-ticket-title"><?php echo $ticketSubject; ?></h1>
                <div class="maha-ticket-number">#<?php echo $ticket->getNumber(); ?></div>
            </div>
        </div>
        <div class="maha-ticket-actions ticket-view-actions">
            <a class="maha-action-btn maha-action-btn--primary" href="tickets.php?a=print&id=<?php echo $ticket->getId(); ?>">
                <i class="icon-print"></i> <?php echo __('Print'); ?>
            </a>
            <?php if ($ticket->hasClientEditableFields() && $thisclient->getId() == $ticket->getUserId()) { ?>
                <a class="maha-action-btn maha-action-btn--secondary" href="tickets.php?a=edit&id=<?php echo $ticket->getId(); ?>">
                    <i class="icon-edit"></i> <?php echo __('Edit'); ?>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="maha-ticket-info-grid">
        <section class="maha-ticket-card">
            <div class="maha-ticket-card__head"><?php echo __('Basic Ticket Information'); ?></div>
            <dl class="maha-ticket-meta">
                <dt><?php echo __('Ticket Status'); ?></dt>
                <dd><span class="maha-ticket-status-pill"><?php echo $ticketStatus; ?></span></dd>
                <dt><?php echo __('Department'); ?></dt>
                <dd><?php echo Format::htmlchars($dept instanceof Dept ? $dept->getName() : ''); ?></dd>
                <dt><?php echo __('Create Date'); ?></dt>
                <dd><?php echo Format::datetime($ticket->getCreateDate()); ?></dd>
            </dl>
        </section>

        <section class="maha-ticket-card">
            <div class="maha-ticket-card__head"><?php echo __('User Information'); ?></div>
            <dl class="maha-ticket-meta">
                <dt><?php echo __('Name'); ?></dt>
                <dd><?php echo mb_convert_case(Format::htmlchars($ticket->getName()), MB_CASE_TITLE); ?></dd>
                <dt><?php echo __('Email'); ?></dt>
                <dd><?php echo Format::htmlchars($ticket->getEmail()); ?></dd>
                <dt><?php echo __('Phone'); ?></dt>
                <dd><?php echo $ticket->getPhoneNumber(); ?></dd>
            </dl>
        </section>
    </div>

    <div class="maha-custom-grid">
        <?php
        $sections = $forms = array();
        foreach (DynamicFormEntry::forTicket($ticket->getId()) as $i => $form) {
            $answers = $form->getAnswers()->exclude(Q::any(array(
                'field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED,
                'field__name__in' => array('subject', 'priority'),
                Q::not(array('field__flags__hasbit' => DynamicFormField::FLAG_CLIENT_VIEW)),
            )));
            foreach ($answers as $j => $a) {
                if ($v = $a->display())
                    $sections[$i][$j] = array($v, $a);
            }
            $forms[$i] = $form->getTitle();
        }
        foreach ($sections as $i => $answers) {
        ?>
            <table class="custom-data" cellspacing="0" cellpadding="4" width="100%" border="0">
                <tr>
                    <td colspan="2" class="headline flush-left"><?php echo $forms[$i]; ?></td>
                </tr>
                <?php foreach ($answers as $A) {
                    list($v, $a) = $A; ?>
                    <tr>
                        <th><?php echo $a->getField()->get('label'); ?>:</th>
                        <td><?php echo $v; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

    <div class="maha-thread-card">
    <?php
    $email = $thisclient->getUserName();
    $clientId = TicketUser::lookupByEmail($email)->getId();

    $ticket->getThread()->render(
        array('M', 'R', 'user_id' => $clientId),
        array(
            'mode' => Thread::MODE_CLIENT,
            'html-id' => 'ticketThread'
        )
    );
    ?>
    </div>
    <?php
    if ($blockReply = $ticket->isChild() && $ticket->getMergeType() != 'visual')
        $warn = sprintf(
            __('This Ticket is Merged into another Ticket. Please go to the %s%d%s to reply.'),
            '<a href="tickets.php?id=',
            $ticket->getPid(),
            '" style="text-decoration:underline">Parent</a>'
        );
    ?>

    <div class="clear" style="padding-bottom:10px;"></div>
    <?php if ($errors['err']) { ?>
        <div id="msg_error"><?php echo $errors['err']; ?></div>
    <?php } elseif ($msg) { ?>
        <div id="msg_notice"><?php echo $msg; ?></div>
    <?php } elseif ($warn) { ?>
        <div id="msg_warning"><?php echo $warn; ?></div>
    <?php }
    if ((!$ticket->isClosed() || $ticket->isReopenable()) && !$blockReply) { ?>
        <form id="reply" class="maha-reply-card" action="tickets.php?id=<?php echo $ticket->getId();
                                                ?>#reply" name="reply" method="post" enctype="multipart/form-data">
            <?php csrf_token(); ?>
            <h2><?php echo __('Post a Reply'); ?></h2>
            <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
            <input type="hidden" name="a" value="reply">
            <div>
                <p class="reply-help"><em><?php
                        echo __('To best assist you, we request that you be specific and detailed'); ?></em>
                    <font class="error">* <?php echo $errors['message']; ?></font>
                </p>
                <textarea name="<?php echo $messageField->getFormName(); ?>" id="message" cols="50" rows="9" wrap="soft"
                    class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> draft" <?php
                                        list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.client', $ticket->getId(), $info['message']);
                                        echo $attrs; ?>><?php echo $draft ?: $info['message'];
                                                        ?></textarea>
                <?php
                if ($messageField->isAttachmentsEnabled()) {
                    print $attachments->render(array('client' => true));
                } ?>
            </div>
            <?php
            if ($ticket->isClosed() && $ticket->isReopenable()) { ?>
                <div class="warning-banner">
                    <?php echo __('Ticket will be reopened on message post'); ?>
                </div>
            <?php } ?>
            <p class="maha-action-group" style="text-align:center">
                <input type="submit" class="maha-action-btn maha-action-btn--primary" value="<?php echo __('Post Reply'); ?>">
                <input type="reset" id="resetButton" class="maha-action-btn maha-action-btn--danger" value="<?php echo __('Reset'); ?>">
                <input type="button" id="cancelButton" class="maha-action-btn maha-action-btn--secondary" value="<?php echo __('Cancel'); ?>" onClick="history.go(-1)">
            </p>
        </form>
    <?php
    } ?>
    <script type="text/javascript">
        <?php
        // Hover support for all inline images
        $urls = array();
        foreach (
            AttachmentFile::objects()->filter(array(
                'attachments__thread_entry__thread__id' => $ticket->getThreadId(),
                'attachments__inline' => true,
            )) as $file
        ) {
            $urls[strtolower($file->getKey())] = array(
                'download_url' => $file->getDownloadUrl(['type' => 'H']),
                'filename' => $file->name,
            );
        } ?>
        showImagesInline(<?php echo JsonDataEncoder::encode($urls); ?>);
    </script>
</div>