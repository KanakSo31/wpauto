<?php
/*
Plugin Name: Daily Post and Comment Count
Description: Sends an email to the admin at the end of the day with the number of posts created and the number of comments received.
*/

// Send email function
function send_post_comment_count() {
    $post_count = wp_count_posts()->publish;
    $comment_count = wp_count_comments()->approved;
    $to = get_option('admin_email');
    $subject = 'Daily Post and Comment Count';
    $message = 'Number of posts created today: ' . $post_count . '\r\n';
    $message .= 'Number of comments received today: ' . $comment_count . '\r\n';
    $headers = 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>' . '\r\n';
    wp_mail($to, $subject, $message, $headers);
}

// Schedule email to be sent at the end of the day
function schedule_email() {
    if (!wp_next_scheduled('send_post_comment_count')) {
        wp_schedule_event(strtotime('today 11:59pm'), 'daily', 'send_post_comment_count');
    }
}
add_action('wp', 'schedule_email');

// Unschedule email when plugin is deactivated
function unschedule_email() {
    wp_clear_scheduled_hook('send_post_comment_count');
}
register_deactivation_hook(__FILE__, 'unschedule_email');
