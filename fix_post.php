<?php
$content = file_get_contents("/tmp/post_content_correct.txt");
if ($content === false) {
    echo "ERROR: Cannot read file\n";
    exit(1);
}
$result = wp_update_post(array("ID" => 21698, "post_content" => $content));
if ($result == 0) {
    echo "ERROR: wp_update_post failed\n";
    exit(1);
}
echo "OK: Post $result updated\n";
