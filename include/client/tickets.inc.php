<?php
if (!defined('OSTCLIENTINC') || !is_object($thisclient) || !$thisclient->isValid()) die('Access Denied');

$settings = &$_SESSION['client:Q'];

// Unpack search, filter, and sort requests
if (isset($_REQUEST['clear']))
    $settings = array();
if (isset($_REQUEST['keywords'])) {
    $settings['keywords'] = $_REQUEST['keywords'];
}
if (isset($_REQUEST['topic_id'])) {
    $settings['topic_id'] = $_REQUEST['topic_id'];
}
if (isset($_REQUEST['status'])) {
    $settings['status'] = $_REQUEST['status'];
}

$org_tickets = $thisclient->canSeeOrgTickets();
if ($settings['keywords']) {
    // Don't show stat counts for searches
    $openTickets = $closedTickets = -1;
} elseif ($settings['topic_id']) {
    $openTickets = $thisclient->getNumTopicTicketsInState(
        $settings['topic_id'],
        'open',
        $org_tickets
    );
    $closedTickets = $thisclient->getNumTopicTicketsInState(
        $settings['topic_id'],
        'closed',
        $org_tickets
    );
} else {
    $openTickets = $thisclient->getNumOpenTickets($org_tickets);
    $closedTickets = $thisclient->getNumClosedTickets($org_tickets);
}

$tickets = Ticket::objects();

$qs = array();
$status = null;

$sortOptions = array(
    'id' => 'number',
    'subject' => 'cdata__subject',
    'status' => 'status__name',
    'dept' => 'dept__name',
    'date' => 'created'
);
$orderWays = array('DESC' => '-', 'ASC' => '');
//Sorting options...
$order_by = $order = null;
$sort = ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'date';
if ($sort && $sortOptions[$sort])
    $order_by = $sortOptions[$sort];

$order_by = $order_by ?: $sortOptions['date'];
if ($_REQUEST['order'] && !is_null($orderWays[strtoupper($_REQUEST['order'])]))
    $order = $orderWays[strtoupper($_REQUEST['order'])];
else
    $order = $orderWays['DESC'];

$x = $sort . '_sort';
$$x = ' class="' . strtolower($_REQUEST['order'] ?: 'desc') . '" ';

$basic_filter = Ticket::objects();
if ($settings['topic_id']) {
    $basic_filter = $basic_filter->filter(array('topic_id' => $settings['topic_id']));
}

if ($settings['status'])
    $status = strtolower($settings['status']);
switch ($status) {
    default:
        $status = 'open';
    case 'open':
    case 'closed':
        $results_type = ($status == 'closed') ? __('Closed Tickets') : __('Open Tickets');
        $basic_filter->filter(array('status__state' => $status));
        break;
}

// Add visibility constraints — use a union query to use multiple indexes,
// use UNION without "ALL" (false as second parameter to union()) to imply
// unique values
$visibility = $basic_filter->copy()
    ->values_flat('ticket_id')
    ->filter(array('user_id' => $thisclient->getId()));

// Add visibility of Tickets where the User is a Collaborator if enabled
if ($cfg->collaboratorTicketsVisibility())
    $visibility = $visibility
        ->union(
            $basic_filter->copy()
                ->values_flat('ticket_id')
                ->filter(array('thread__collaborators__user_id' => $thisclient->getId())),
            false
        );

if ($thisclient->canSeeOrgTickets()) {
    $visibility = $visibility->union(
        $basic_filter->copy()->values_flat('ticket_id')
            ->filter(array('user__org_id' => $thisclient->getOrgId())),
        false
    );
}

// Perform basic search
if ($settings['keywords']) {
    $q = trim($settings['keywords']);
    if (is_numeric($q)) {
        $tickets->filter(array('number__startswith' => $q));
    } elseif (strlen($q) > 2) { //Deep search!
        // Use the search engine to perform the search
        $tickets = $ost->searcher->find($q, $tickets);
    }
}

$tickets->distinct('ticket_id');

TicketForm::ensureDynamicDataView();

$total = $visibility->count();
$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
$pageNav = new Pagenate($total, $page, PAGE_LIMIT);
$qstr = '&amp;' . Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('tickets.php', $qs);
$tickets->filter(array('ticket_id__in' => $visibility));
$pageNav->paginate($tickets);

$showing = $total ? $pageNav->showing() : "";
if (!$results_type) {
    $results_type = ucfirst($status) . ' ' . __('Tickets');
}
$showing .= ($status) ? (' ' . $results_type) : ' ' . __('All Tickets');
if ($search)
    $showing = __('Search Results') . ": $showing";

$negorder = $order == '-' ? 'ASC' : 'DESC'; //Negate the sorting

$tickets->order_by($order . $order_by);
$tickets->values(
    'ticket_id',
    'number',
    'created',
    'isanswered',
    'source',
    'status_id',
    'status__state',
    'status__name',
    'cdata__subject',
    'dept_id',
    'dept__name',
    'dept__ispublic',
    'user__default_email__address',
    'user_id'
);

/* MahaAgX-style sort URLs (dataset catalogue pattern) */
$maha_sort_choices = array(
    array('sort' => 'subject', 'order' => 'ASC', 'label' => __('A-Z')),
    array('sort' => 'subject', 'order' => 'DESC', 'label' => __('Z-A')),
    array('sort' => 'date', 'order' => 'ASC', 'label' => __('Old to New')),
    array('sort' => 'date', 'order' => 'DESC', 'label' => __('New to Old')),
);
$req_order_upper = strtoupper($_REQUEST['order'] ?: 'DESC');
$maha_current_sort_label = null;
foreach ($maha_sort_choices as $_msc) {
    if (strtolower($sort) === $_msc['sort'] && $req_order_upper === $_msc['order']) {
        $maha_current_sort_label = $_msc['label'];
        break;
    }
}
if (!$maha_current_sort_label) {
    $_sl = array(
        'id' => __('Ticket'),
        'date' => __('Created'),
        'status' => __('Status'),
        'subject' => __('Subject'),
        'dept' => __('Department'),
    );
    $maha_current_sort_label = isset($_sl[$sort]) ? $_sl[$sort] : __('Custom');
}

?>
<style>
.tickets-page-container {
    width: 100%;
    padding: 2rem 4rem;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    min-height: 60vh;
    margin-bottom: 3rem;
}

.tickets-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.tickets-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a2e05;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.tickets-title .refresh-icon {
    width: 24px;
    height: 24px;
    cursor: pointer;
    transition: transform 0.3s;
}

.tickets-title .refresh-icon:hover {
    transform: rotate(180deg);
}

.tickets-status-tabs {
    display: flex;
    gap: 0.5rem;
    padding: 0;
    border-radius: 0;
    background: transparent;
    flex-wrap: wrap;
}

.status-tab {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0.55rem 1.25rem;
    border-radius: 8px;
    border: 1px solid #00d084;
    background: #ffffff;
    font-size: 0.875rem;
    font-weight: 500;
    color: #00b371;
    text-decoration: none;
    transition: 150ms ease;
    line-height: 1;
    white-space: nowrap;
}

.status-tab:hover {
    background: #f0fffa;
    border-color: #00b371;
    color: #00b371;
    transform: translateY(-1px);
}

.status-tab.active {
    background: #00d084;
    border-color: #00d084;
    color: #0a0a0a;
    box-shadow: 0 10px 24px rgba(0, 208, 132, 0.18);
}

.search-filter-section {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    margin-bottom: 2rem;
}

.search-filter-row {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-input-group {
    flex: 1;
    min-width: 300px;
    display: flex;
    gap: 0.5rem;
}

.search-input-group input[type="text"] {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s;
}

.search-input-group input[type="text"]:focus {
    outline: none;
    border-color: #65a30d;
    box-shadow: 0 0 0 3px rgba(101, 163, 13, 0.1);
}

.search-btn {
    background: #65a30d;
    color: #fff;
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.search-btn:hover {
    background: #4d7c0a;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-label {
    font-size: 14px;
    font-weight: 600;
    color: #1a2e05;
}

.filter-select {
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
}

.filter-select:focus {
    outline: none;
    border-color: #65a30d;
}

/* MahaAgX-style selects (reference: dataset catalogue sort + filters) */
.filter-select.maha-filter-select {
    appearance: none;
    -webkit-appearance: none;
    min-width: 12rem;
    padding: 0.65rem 2.75rem 0.65rem 1rem;
    border: 1px solid #00b371;
    border-radius: 10px;
    background-color: #ecfdf5;
    color: #047857;
    font-weight: 600;
    font-size: 0.875rem;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2300b371' stroke-width='2'%3E%3Cpath d='M8 9l4-4 4 4M8 15l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.65rem center;
    background-size: 16px 16px;
}

.filter-select.maha-filter-select:focus {
    border-color: #00d084;
    box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.15);
}

.maha-sort-wrap {
    position: relative;
    flex-shrink: 0;
}

.maha-sort__trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.65rem 2.75rem 0.65rem 1rem;
    border: 1px solid #00b371;
    border-radius: 10px;
    background: #ecfdf5;
    color: #047857;
    font-family: inherit;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    white-space: nowrap;
}

.maha-sort__trigger:hover {
    border-color: #00d084;
}

.maha-sort__trigger:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.15);
}

.maha-sort__trigger-label {
    color: #047857;
}

.maha-sort__trigger-value {
    color: #059669;
}

.maha-sort__chevron {
    position: absolute;
    right: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #00b371;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1px;
    line-height: 0;
}

.maha-sort__trigger {
    position: relative;
    padding-right: 2.5rem;
}

.maha-sort__dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 6px);
    width: max-content;
    min-width: 220px;
    padding: 0.5rem;
    margin: 0;
    list-style: none;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.12), 0 2px 8px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.06);
    z-index: 50;
}

.maha-sort__dropdown.is-open {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.maha-sort__option {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.55rem 0.65rem;
    border-radius: 999px;
    text-decoration: none;
    color: #1e293b;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.15s ease, color 0.15s ease;
}

.maha-sort__option:hover {
    background: #f8fafc;
}

.maha-sort__option.is-selected {
    background: #d1fae5;
    color: #047857;
}

.maha-sort__cb {
    position: relative;
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    background: #fff;
    box-sizing: border-box;
}

.maha-sort__option.is-selected .maha-sort__cb {
    background: #00d084;
    border-color: #00b371;
}

.maha-sort__option.is-selected .maha-sort__cb::after {
    content: '';
    position: absolute;
    left: 2px;
    top: 2px;
    right: 2px;
    bottom: 2px;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 13l4 4L19 7'/%3E%3C/svg%3E") center / contain no-repeat;
}

.clear-filters-link {
    color: #dc2626;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.clear-filters-link:hover {
    text-decoration: underline;
}

.tickets-table-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    margin-bottom: 3rem;
}

.tickets-table {
    width: 100%;
    border-collapse: collapse;
}

.tickets-table thead {
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
}

.tickets-table th {
    padding: 1rem 1.5rem;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: #1a2e05;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tickets-table th a {
    color: #1a2e05;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tickets-table th a:hover {
    color: #65a30d;
}

.tickets-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s;
}

.tickets-table tbody tr:hover {
    background: #f9fafb;
}

.tickets-table tbody tr:last-child {
    border-bottom: none;
}

.tickets-table td {
    padding: 1.25rem 1.5rem;
    font-size: 15px;
    color: #374151;
    line-height: 1.6;
}

.ticket-number-link {
    color: #65a30d;
    text-decoration: none;
    font-weight: 600;
    font-family: 'Courier New', monospace;
    font-size: 16px;
}

.ticket-number-link:hover {
    text-decoration: underline;
}

.ticket-subject-link {
    color: #1a2e05;
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 320px;
}

.ticket-subject-link:hover {
    color: #65a30d;
}

.ticket-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.ticket-status-open {
    background: #dcfce7;
    color: #166534;
}

.ticket-status-closed {
    background: #f3f4f6;
    color: #6b7280;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #9ca3af;
}

.empty-state svg {
    width: 80px;
    height: 80px;
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.empty-state p {
    font-size: 16px;
    margin: 0;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.pagination-wrapper a {
    color: #65a30d;
    text-decoration: none;
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.pagination-wrapper a:hover {
    background: #f0fdf4;
}

@media (max-width: 768px) {
    .tickets-page-container {
        padding: 1.5rem;
    }
    
    .tickets-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .search-filter-row {
        flex-direction: column;
    }
    
    .search-input-group {
        width: 100%;
    }
    
    .tickets-table th,
    .tickets-table td {
        padding: 0.75rem;
        font-size: 13px;
    }
}
</style>

<div class="tickets-page-container">
    <div class="tickets-header">
        <h1 class="tickets-title">
            <a href="<?php echo Format::htmlchars($_SERVER['REQUEST_URI'] ?? 'tickets.php'); ?>" style="color: #1a2e05; text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                <svg class="refresh-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4v6h6M20 20v-6h-6M4 10a8 8 0 0113.66-5.66M20 14a8 8 0 01-13.66 5.66" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?php echo __('Tickets'); ?>
            </a>
        </h1>

        <div class="tickets-status-tabs">
            <?php if ($openTickets) { ?>
                <a class="status-tab <?php if ($status == 'open') echo 'active'; ?>"
                   href="?<?php echo Http::build_query(array('a' => 'search', 'status' => 'open')); ?>">
                    <?php echo __('Open');
                    if ($openTickets > 0) echo sprintf(' (%d)', $openTickets); ?>
                </a>
            <?php }
            if ($closedTickets) { ?>
                <a class="status-tab <?php if ($status == 'closed') echo 'active'; ?>"
                   href="?<?php echo Http::build_query(array('a' => 'search', 'status' => 'closed')); ?>">
                    <?php echo __('Closed');
                    if ($closedTickets > 0) echo sprintf(' (%d)', $closedTickets); ?>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="search-filter-section">
        <form action="tickets.php" method="get" id="ticketSearchForm">
            <input type="hidden" name="a" value="search">
            <div class="search-filter-row">
                <div class="search-input-group">
                    <input type="text" 
                           name="keywords" 
                           placeholder="<?php echo __('Search tickets by number or subject...'); ?>" 
                           value="<?php echo Format::htmlchars($settings['keywords']); ?>">
                    <button type="submit" class="search-btn"><?php echo __('Search'); ?></button>
                </div>
                
                <div class="maha-sort-wrap" data-maha-sort>
                    <button type="button" class="maha-sort__trigger" id="client-maha-sort-btn"
                            aria-haspopup="listbox" aria-expanded="false"
                            aria-controls="client-maha-sort-menu">
                        <span class="maha-sort__trigger-label"><?php echo __('Sort'); ?>:</span>
                        <span class="maha-sort__trigger-value"><?php echo Format::htmlchars($maha_current_sort_label); ?></span>
                        <span class="maha-sort__chevron" aria-hidden="true">
                            <svg width="12" height="5" viewBox="0 0 24 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8L12 2L18 8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg width="12" height="5" viewBox="0 0 24 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 2L12 8L6 2" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </button>
                    <div class="maha-sort__dropdown" id="client-maha-sort-menu" role="listbox"
                         aria-labelledby="client-maha-sort-btn">
                        <?php foreach ($maha_sort_choices as $_ch) {
                            $_sel = (strtolower($sort) === $_ch['sort'] && $req_order_upper === $_ch['order']);
                            $_q = $_GET;
                            $_q['sort'] = $_ch['sort'];
                            $_q['order'] = $_ch['order'];
                            unset($_q['p']);
                            $_href = 'tickets.php?' . http_build_query($_q);
                            ?>
                        <a class="maha-sort__option<?php if ($_sel) echo ' is-selected'; ?>" role="option"
                           aria-selected="<?php echo $_sel ? 'true' : 'false'; ?>"
                           href="<?php echo Format::htmlchars($_href); ?>">
                            <span class="maha-sort__cb" aria-hidden="true"></span>
                            <span><?php echo Format::htmlchars($_ch['label']); ?></span>
                        </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label"><?php echo __('Help Topic'); ?>:</label>
                    <select name="topic_id" class="filter-select maha-filter-select" onchange="this.form.submit();">
                        <option value=""><?php echo __('All Topics'); ?></option>
                        <?php
                        foreach (Topic::getHelpTopics(true) as $id => $name) {
                            $count = $thisclient->getNumTopicTickets($id, $org_tickets);
                            if ($count == 0) continue;
                        ?>
                            <option value="<?php echo $id; ?>" <?php if ($settings['topic_id'] == $id) echo 'selected="selected"'; ?>>
                                <?php echo sprintf('%s (%d)', Format::htmlchars($name), $count); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            
            <?php if ($settings['keywords'] || $settings['topic_id'] || $_REQUEST['sort']) { ?>
                <a href="?clear" class="clear-filters-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <?php echo __('Clear all filters and sort'); ?>
                </a>
            <?php } ?>
        </form>
    </div>

    <div class="tickets-table-card">
        <table class="tickets-table">
            <thead>
                <tr>
                    <th>
                        <a href="tickets.php?sort=ID&order=<?php echo $negorder; ?><?php echo $qstr; ?>">
                            <?php echo __('Ticket'); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </a>
                    </th>
                    <th>
                        <a href="tickets.php?sort=date&order=<?php echo $negorder; ?><?php echo $qstr; ?>">
                            <?php echo __('Created'); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </a>
                    </th>
                    <th>
                        <a href="tickets.php?sort=status&order=<?php echo $negorder; ?><?php echo $qstr; ?>">
                            <?php echo __('Status'); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </a>
                    </th>
                    <th>
                        <a href="tickets.php?sort=subject&order=<?php echo $negorder; ?><?php echo $qstr; ?>">
                            <?php echo __('Subject'); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </a>
                    </th>
                    <th>
                        <a href="tickets.php?sort=dept&order=<?php echo $negorder; ?><?php echo $qstr; ?>">
                            <?php echo __('Department'); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subject_field = TicketForm::objects()->one()->getField('subject');
                $defaultDept = Dept::getDefaultDeptName();
                if ($tickets->exists(true)) {
                    foreach ($tickets as $T) {
                        $dept = $T['dept__ispublic']
                            ? Dept::getLocalById($T['dept_id'], 'name', $T['dept__name'])
                            : $defaultDept;
                        $subject = $subject_field->display(
                            $subject_field->to_php($T['cdata__subject']) ?: $T['cdata__subject']
                        );
                        $status = TicketStatus::getLocalById($T['status_id'], 'value', $T['status__name']);
                        $ticketNumber = $T['number'];
                        $isBold = $T['isanswered'] && !strcasecmp($T['status__state'], 'open');
                        $thisclient->getId() != $T['user_id'] ? $isCollab = true : $isCollab = false;
                ?>
                        <tr>
                            <td>
                                <a class="ticket-number-link" 
                                   href="tickets.php?id=<?php echo $T['ticket_id']; ?>"
                                   style="<?php if ($isBold) echo 'font-weight: 700;'; ?>">
                                    #<?php echo $ticketNumber; ?>
                                </a>
                            </td>
                            <td><?php echo Format::date($T['created']); ?></td>
                            <td>
                                <span class="ticket-status-badge ticket-status-<?php echo strtolower($T['status__state']); ?>">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                            <td>
                                <a class="ticket-subject-link" 
                                   href="tickets.php?id=<?php echo $T['ticket_id']; ?>"
                                   style="<?php if ($isBold) echo 'font-weight: 700;'; ?>">
                                    <?php if ($isCollab) echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-right:4px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>'; ?>
                                    <?php echo $subject; ?>
                                </a>
                            </td>
                            <td><?php echo $dept; ?></td>
                        </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p><?php echo __('No tickets found'); ?></p>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    if ($total) {
        echo '<div class="pagination-wrapper">' . __('Page') . ': ' . $pageNav->getPageLinks() . '</div>';
    }
    ?>
<script type="text/javascript">
$(function () {
    var $root = $('[data-maha-sort]');
    if (!$root.length) return;
    var $btn = $root.find('.maha-sort__trigger');
    var $menu = $root.find('.maha-sort__dropdown');
    $btn.on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var open = !$menu.hasClass('is-open');
        $menu.toggleClass('is-open', open);
        $btn.attr('aria-expanded', open ? 'true' : 'false');
    });
    $(document).on('click', function () {
        $menu.removeClass('is-open');
        $btn.attr('aria-expanded', 'false');
    });
    $root.on('click', function (e) {
        e.stopPropagation();
    });
});
</script>
</div>
<?php