<?php
namespace poshtvan\app;
class tickets
{
    const TICKET_LIST_LIMIT = 10;
    const TICKET_TABLE_NAME = 'mihanticket_tickets';
    
    const STATUS_CODE_PENDING = 1;
    const STATUS_CODE_INVESTIGATION = 2;
    const STATUS_CODE_ANSWERED = 3;
    const STATUS_CODE_CLOSED = 4;
    const STATUS_USER_SOLVED_TICKET = 'poshtvan_user_solved';
    
    private static function get_db(&$tbl_name)
    {
        global $wpdb;
        $tbl_name = $wpdb->prefix . 'mihanticket_tickets';
        return $wpdb;
    }
    static function add_new($user_id, $subject, $content, $parent_id=null, $isAutoTicket=false)
    {
        $db = self::get_db($tbl_name);
        $date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
        $data = [
            'user_id' => $user_id,
            'subject' => $subject,
            'content' => stripslashes($content),
            'parent_ticket_id' => $parent_id,
            'created_date' => $date,
            'update_date' => $date
        ];
        $insert_res = $db->insert(
            $tbl_name,
            $data
        );
        if($insert_res && $parent_id)
        {
            self::update_ticket_date($parent_id, $date);
        }
        if(!$isAutoTicket)
        {
            do_action('poshtvan/after_submit_ticket', $db->insert_id, $user_id, $parent_id, $date);
        }
        return $insert_res ? $db->insert_id : false;
    }
    static function update_ticket_date($ticket_id, $date=null)
    {
        if(!$ticket_id)
        {
            return false;
        }
        if(!$date)
        {
            $date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
        }
        $db = self::get_db($tbl_name);
        return $db->update($tbl_name, ['update_date' => $date], ['id' => intval($ticket_id)], ['%s'], ['%d']);
    }
    static function update_ticket($ticket_id, $content)
    {
        if(!$ticket_id || !$content)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        return $db->update(
            $tbl_name,
            ['content' => $content],
            ['id' => intval($ticket_id)],
            ['%s'],
            ['%d'],
        );
    }
    static function get_user_ticket_list($user_id, $limit=null, $offset=0)
    {
        $db = self::get_db($tbl_name);
        $query = "SELECT * FROM {$tbl_name} WHERE user_id=%d and parent_ticket_id IS NULL ORDER BY update_date DESC";
        $prepare_data[] = $user_id;
        if($limit)
        {
            $query .= " limit %d offset %d";
            $prepare_data[] = $limit;
            $prepare_data[] = intval($offset);
        }
        return $db->get_results($db->prepare($query, $prepare_data));
    }
    static function get_tickets($limit=null, $offset=0, $ticketStatus = [])
    {
        $db = self::get_db($tbl_name);
        $query = "SELECT id,subject,status FROM {$tbl_name} WHERE parent_ticket_id IS NULL";
        if($ticketStatus && $ticketStatus !== 'all' && !(is_array($ticketStatus) && in_array('all', $ticketStatus)))
        {
            $ticketStatus = !is_array($ticketStatus) ? [$ticketStatus] : $ticketStatus;
            $ticketStatus = implode('", "', $ticketStatus);
            $query .= ' AND status IN ("'.$ticketStatus .'")';
        }
        $query .= " ORDER BY status,update_date DESC,id DESC";
        $prepare_data = [];
        if($limit)
        {
            $query .= " limit %d offset %d";
            $prepare_data[] = $limit;
            $prepare_data[] = intval($offset);
        }
        return $db->get_results($db->prepare($query, $prepare_data));
    }

    static function getRateList()
    {
        return [
            1 => [
                'name' => 'very_bad',
                'title' => esc_html__('Very bad', 'poshtvan'),
                'icon' => 'star-1.png',
            ],
            2 => [
                'name' => 'bad',
                'title' => esc_html__('Bad', 'poshtvan'),
                'icon' => 'star-2.png',
            ],
            3 => [
                'name' => 'good',
                'title' => esc_html__('Good', 'poshtvan'),
                'icon' => 'star-3.png',
            ],
            4 => [
                'name' => 'very_good',
                'title' => esc_html__('Very Good', 'poshtvan'),
                'icon' => 'star-4.png',
            ],
            5 => [
                'name' => 'perfect',
                'title' => esc_html__('Perfect', 'poshtvan'),
                'icon' => 'star-5.png',
            ],
        ];
    }
    static function getRateTitle($rateValue)
    {
        $list = self::getRateList();
        return isset($list[$rateValue]) ? $list[$rateValue]['title'] : null;
    }
    static function get_status_list()
    {
        return apply_filters('poshtvan/tickets/ticket_status_list', [
            self::STATUS_CODE_PENDING => ['name' => 'pending', 'title' => esc_html__('Pending', 'poshtvan')],
            self::STATUS_CODE_INVESTIGATION => ['name' => 'investigation', 'title' => esc_html__('On Hold', 'poshtvan')],
            self::STATUS_CODE_ANSWERED => ['name' => 'answered', 'title' => esc_html__('Answered', 'poshtvan')],
            self::STATUS_CODE_CLOSED => ['name' => 'closed', 'title' => esc_html__('Closed', 'poshtvan')],
        ]);
    }
    static function get_status_list_for_user()
    {
        $list = self::get_status_list();
        unset($list[self::STATUS_CODE_CLOSED]);
        return $list;
    }
    static function getAutoTicketStatusList()
    {
        $list = self::get_status_list();
        $list[] = [
            'name' => self::STATUS_USER_SOLVED_TICKET,
            'title' => __('User sovled ticket', 'poshtvan'),
        ];
        return $list;
    }
    static function get_status_code($status_name)
    {
        $list = self::get_status_list();
        foreach($list as $status_code => $item)
        {
            if($item['name'] == $status_name)
            {
                return $status_code;
            }
        }
    }
    static function status_convert($status_code)
    {
        $status_list = self::get_status_list();
        return isset($status_list[$status_code]) ? $status_list[$status_code] : $status_list[self::STATUS_CODE_PENDING];
    }
    static function get_ticket_status_code($ticket_id)
    {
        $db = self::get_db($tbl_name);
        $query = "SELECT status FROM {$tbl_name} WHERE id=%d";
        return $db->get_var($db->prepare($query, $ticket_id));
    }
    static function get_ticket_status_data($ticket_id)
    {
        $code = self::get_ticket_status_code($ticket_id);
        return $code ? self::status_convert($code) : false;
    }
    static function getTicket($ticketID, $cols=['id'])
    {
        if(!$ticketID) return false;
        $db = self::get_db($tableName);
        $method = 'get_row';
        if(is_array($cols))
        {
            $method = count($cols) > 1 ? 'get_row' : 'get_var';
            $cols = implode(', ', $cols);
        }elseif($cols === '*')
        {
            $method = 'get_row';
        }else{
            $method = 'get_var';
        }
        $sql = "SELECT {$cols} FROM {$tableName} WHERE id=%d";
        return $db->{$method}($db->prepare($sql, $ticketID));
    }
    static function get_ticket_data($focus=false, $cols=[], $where=1, $prepare_data=[])
    {
        $db = self::get_db($tbl_name);
        if(is_array($where))
        {
            $where_args = [];
            foreach($where as $key => $value)
            {
                if(is_numeric($key))
                {
                    $where_args[] = $value;
                }else{
                    $where_args[] = $key . '=' . $value;
                }
            }
            $where = implode(' AND ', $where_args);
        }
        $method = 'get_results';
        if($focus)
        {
            $method = !$cols || !is_array($cols) || (is_array($cols) && count($cols) > 1) ? 'get_row' : 'get_var';
        }
        $cols = $cols && is_array($cols) ? implode(', ', $cols) : '*';
        $query = "SELECT {$cols} FROM {$tbl_name} WHERE {$where} and parent_ticket_id IS NULL";
        if($prepare_data)
        {
            $query = $db->prepare($query, $prepare_data);
        }
        return $db->{$method}($query);
    }
    static function get_ticket_content($ticket_id, $user_id=null)
    {
        $db = self::get_db($tbl_name);
        $query = "SELECT content FROM {$tbl_name} WHERE id=%d";
        $prepare_data = [$ticket_id];
        if($user_id)
        {
            $query .= ' AND user_id=%d';
            $prepare_data[] = $user_id;
        }
        return $db->get_var($db->prepare($query, $prepare_data));
    }
    static function get_ticket_replies($ticket_id, $user_id=null)
    {
        $db = self::get_db($tbl_name);
        $query = "SELECT replies.* FROM $tbl_name as replies JOIN {$tbl_name} as tickets ON replies.parent_ticket_id = tickets.id where tickets.id=%d";
        $prepare_data = [$ticket_id];
        if($user_id)
        {
            $query .= ' AND tickets.user_id=%d';
            $prepare_data[] = $user_id;
        }
        return $db->get_results($db->prepare($query, $prepare_data));
    }
    static function getOperatorReplies($ticketID, $cols='*')
    {
        $db = self::get_db($tbl_name);
        if(is_array($cols))
        {
            $cols = array_map(function($col){ return "replies.{$col}"; }, $cols);
            $cols = implode(', ', $cols);
        }else{
            $cols = 'replies.' . $cols;
        }
        $query = "SELECT {$cols} FROM {$tbl_name} as replies JOIN {$tbl_name} as tickets ON replies.parent_ticket_id=tickets.id where tickets.id=%d AND tickets.user_id!=replies.user_id";
        return $db->get_results($db->prepare($query, $ticketID));
    }
    static function get_ticket_owner($ticket_id)
    {
        $db = self::get_db($tbl_name);
        $query = "SELECT user_id FROM {$tbl_name} WHERE id=%d";
        $res = $db->get_var($db->prepare($query, $ticket_id));
        return $res ? intval($res) : false;
    }
    static function change_ticket_status($ticket_id, $status_code, $owner_id=null)
    {
        if(!$ticket_id || !$status_code)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        $where = ['id' => $ticket_id];
        $where_format = ['%d'];
        if($owner_id)
        {
            $where = array_merge($where, ['user_id' => $owner_id]);
            $where_format[] = '%d';
        }
        do_action('poshtvan_before_ticket_change_status', $ticket_id, $status_code, $owner_id);
        $updateResult = $db->update(
            $tbl_name,
            ['status' => $status_code],
            $where,
            ['%s'],
            $where_format,
        );
        do_action('poshtvan_after_ticket_change_status', $ticket_id, $status_code, $owner_id, $updateResult);
        return $updateResult;
    }
    static function add_attachment($ticketId, $fileData, &$msg='')
    {
        $uploaded_base_dir = file_uploader::uploadFile($fileData, $msg);
        if (!$uploaded_base_dir) {
            return false;
        }
        ticket_meta::update_meta($ticketId, 'attachment_name', $uploaded_base_dir);
    }
    static function search_in_ticket($needle)
    {
        $db = self::get_db($tbl_name);
        $needle = '%' . $db->esc_like($needle) . '%';
        // search in ticket id
        // search in ticket name
        // search in user email
        $in_name_query = "SELECT tickets.id,tickets.subject,tickets.status
            FROM {$tbl_name} as tickets
            INNER JOIN {$db->users} as users ON tickets.user_id=users.ID
            WHERE (
                    tickets.subject LIKE %s
                    OR tickets.id LIKE %s
                    OR users.user_email LIKE %s
                )
            AND parent_ticket_id IS NULL
            ORDER BY tickets.status,tickets.update_date DESC,tickets.id DESC";
        $in_name = $db->get_results($db->prepare($in_name_query, $needle, $needle, $needle));
        //TODO: search in ticket attachment name
        //TODO: search in ticket content
        return $in_name ? $in_name : false;
    }
    static function get_new_tickets_count()
    {
        $db = self::get_db($tbl_name);
        $query = "SELECT count(*) FROM {$tbl_name} where status=1 and parent_ticket_id IS NULL";
        return $db->get_var($query);
    }

    /**
     * fired on poshtvan/new_ticket/before_verification hook
     * fired on poshtvan/reply_ticket/before_submit_reply hook
     */
    static function verifyUserTicketFileUploadingMimeType()
    {
        $fileData = isset($_FILES['fileField']) ? $_FILES['fileField'] : false;
        if($fileData)
        {
            $validateType = file_uploader::validate_file_type($fileData);
            if(!$validateType)
            {
                error::add_error('submit_new_ticket/fields_verification', esc_html__('Invalid File Type', 'poshtvan'));
                return false;
            }
        }
    }
    
    /**
     * fired on poshtvan/admin_ticket/verification hook
     */
    static function verifyAdminTicketFileUploadingMimeType()
    {
        $fileData = isset($_FILES['fileField']) ? $_FILES['fileField'] : false;
        if($fileData)
        {
            $validateType = file_uploader::validate_file_type($fileData);
            if(!$validateType)
            {
                error::add_error('submit_admin_ticket/fields_verification', esc_html__('Invalid File Type', 'poshtvan'));
                return false;
            }
        }
    }

    static function truncateTable()
    {
        $db = self::get_db($tblName);
        return $db->query("TRUNCATE TABLE {$tblName}");
    }

    static function handleSendAutoTicketProcessAfterOperatorChangeTicketStatus($ticketID, $statusCode)
    {
        $statusData = self::status_convert($statusCode);
        $statusCode = $statusData ? $statusData['name'] : false;
        if(!$statusCode)
        {
            return false;
        }

        $autoTicketItems = options::getAutoTicketItems();
        if(!isset($autoTicketItems[$statusCode]) || !is_array($autoTicketItems[$statusCode]))
        {
            return;
        }

        $operatorID = options::getAutoTicketOperatorUser();
        if(!$operatorID)
        {
            return;
        }
        foreach($autoTicketItems[$statusCode] as $autoTicketItem)
        {
            self::add_new($operatorID, null, $autoTicketItem, $ticketID, true);
        }
    }

    static function handleSendAutoTicketProcessAfterUserSolvedTicket($ticketID, $userID)
    {
        $autoTicketItems = options::getAutoTicketItems();
        if(!isset($autoTicketItems[self::STATUS_USER_SOLVED_TICKET]) || !is_array($autoTicketItems[self::STATUS_USER_SOLVED_TICKET]))
        {
            return;
        }
        $operatorID = options::getAutoTicketOperatorUser();
        if(!$operatorID)
        {
            return;
        }
        foreach($autoTicketItems[self::STATUS_USER_SOLVED_TICKET] as $ticketContent)
        {
            self::add_new($operatorID, null, $ticketContent, $ticketID, true);
        }
    }
    
    static function handleAutoCloseTicketEvent()
    {
        if(!options::isTicketAutoCloseActive()) return false;
        $autoCloseDayCounter = options::getAutoCloseDayCounter();

        global $wpdb;
        $tableName = $wpdb->prefix . self::TICKET_TABLE_NAME;

        $batchSize = 100;
        
        $sql = "SELECT x.*
                FROM {$tableName} as t
                JOIN (
                    SELECT thread_id, MAX(activity) as last_activity
                    FROM (
                        SELECT id as thread_id,
                        COALESCE(update_date, created_date) as activity
                        FROM {$tableName}
                        WHERE parent_ticket_id IS NULL AND status=%d
                        UNION ALL
                        SELECT parent_ticket_id as thread_id,
                        COALESCE(update_date, created_date) as activity
                        FROM {$tableName}
                        WHERE parent_ticket_id IS NOT NULL AND parent_ticket_id IN (
                            SELECT id FROM {$tableName} WHERE status=%d
                        )
                    ) u
                    GROUP BY thread_id
                ) as x ON x.thread_id=t.id
                WHERE t.parent_ticket_id IS NULL
                AND t.status = %d
                AND x.last_activity < DATE_SUB(NOW(), INTERVAL %d DAY)
                LIMIT {$batchSize}
                ";
        
        $ticketItems = $wpdb->get_results($wpdb->prepare($sql, self::STATUS_CODE_ANSWERED, self::STATUS_CODE_ANSWERED, self::STATUS_CODE_ANSWERED, $autoCloseDayCounter));

        if(!$ticketItems) return false;

        foreach($ticketItems as $ticketItem)
        {
            self::change_ticket_status($ticketItem->thread_id, self::STATUS_CODE_CLOSED);
            do_action('poshtvan/tickets/after_auto_close_ticket', $ticketItem->thread_id);
        }

        if(count($ticketItems) == $batchSize)
        {
            wp_schedule_single_event(time() + 30, '__poshtvan_handle_auto_close_ticket_event');
        }
        
    }

    static function getTicketRateValue($ticketID)
    {
        if(!$ticketID) return false;
        return (string) ticket_meta::get_meta($ticketID, 'ticket_rate_value');
    }
    static function setTicketRateValue($ticketID, $rateValue, $isMainTicket=false)
    {
        if(!$ticketID || !$rateValue) return false;

        $oldValue = self::getTicketRateValue($ticketID);
        if($oldValue && $oldValue == $rateValue) return true;
        
        $update = ticket_meta::update_meta($ticketID, 'ticket_rate_value', $rateValue);
        
        if($update && $isMainTicket)
        {
            do_action('poshtvan/tickets/after_submit_rate_value', $ticketID, $rateValue);
        }
        return $update;
    }
    static function handleAddRatingToTheReplyItemInUserPanel($isSupporter, $ticketItem)
    {
        if(!options::isTicketRatingActive() || !$isSupporter) return false;
        // check is ticket closed
        $parentTicket = self::getTicket($ticketItem->parent_ticket_id, ['id', 'status']);
        if(!$parentTicket) return false;
        $previewMode = $parentTicket->status == self::STATUS_CODE_CLOSED;

        $ticketID = $ticketItem->id;
        $currentRateValue = self::getTicketRateValue($ticketID);
        
        if($previewMode && !$currentRateValue) return false;
        $viewName = $previewMode ? 'ratings-preview' : 'ratings';
        $ratingView = files::get_file_path('views.user.ticket.' . $viewName);
        $ratingView ? include $ratingView : null;
    }
    static function handleAddRatingToTheReplyItemInAdminPanel($isSupporter, $ticketItem)
    {
        if(!options::isTicketRatingActive() || !$isSupporter) return false;
        $ticketID = $ticketItem->id;
        $currentRateValue = self::getTicketRateValue($ticketID);
        if(!$currentRateValue) return false;
        $ratingView = files::get_file_path('views.admin.ticket.ratings');
        $ratingView ? include $ratingView : null;
    }
    static function handleCalculateTicketRatingAfterAutoCloseTicket($ticketID)
    {
        if(!options::isTicketRatingActive()) return false;

        $mainTicket = self::getTicket($ticketID, ['user_id']);
        if(!$mainTicket) return false;
        $replyItems = self::getOperatorReplies($ticketID, ['id']);
        if(!$replyItems) return false;

        $rateValue = 0;
        $availableRateCount = 0;
        foreach($replyItems as $replyItem)
        {
            $itemRateValue = self::getTicketRateValue($replyItem->id);
            if(!$itemRateValue) continue;
            $availableRateCount++;
            $rateValue += $itemRateValue;
        }
        
        $averageRateValue = $rateValue / $availableRateCount;
        $averageRateValue = is_float($averageRateValue) ? number_format($averageRateValue, 1, '.', '') : (string) $averageRateValue;
        self::setTicketRateValue($ticketID, $averageRateValue, true);
    }
}
