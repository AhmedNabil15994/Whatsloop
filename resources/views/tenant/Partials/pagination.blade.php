@if(isset($data->pagination))
<?php
$current_page = $data->pagination->current_page;
$total_pages = $data->pagination->last_page;
$per_page = 10;
$page = $current_page;

$total = $total_pages;
$adjacents = "2";
$firstlabel = "&laquo; ".trans('pagination.first');
$prevlabel = "&lsaquo; ".trans('pagination.prev');
$nextlabel = trans('pagination.next')." &rsaquo;";
$lastlabel = trans('pagination.last')." &raquo;";

$page = ($page == 0 ? 1 : $page);
$start = ($page - 1) * $per_page;

$first = 1;
$prev = $page - 1;
$next = $page + 1;

//dd($_SERVER['QUERY_STRING']);
$paramsOld = strpos($_SERVER['REQUEST_URI'],'?') != false ? $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'].'?';
//dd($paramsOld);

$url = str_replace('&page='.$prev,'',$paramsOld);
$url = str_replace('?page='.$prev,'?',$url);
$url = str_replace('?page='.$page,'?',$url);
$url = str_replace('&page='.$page,'',$url);

$url .= \Request::getQueryString() != "" ? strpos($_SERVER['REQUEST_URI'],'?page=') != false ? '' : '&' : '';

$lastpage = $total_pages;

$lpm1 = $lastpage - 1;

$pagination = "";
if($lastpage >= 1){
    $pagination .= '<div class="row">';
    $pagination .= '<div class="col-12">';
    $pagination .= '<div class="text-right">';
    $pagination .= "<ul class='pagination pagination-rounded justify-content-end'>";
    //$pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";

    if ($page > 1) $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$prev}'><span aria-hidden='true'>«</span><span class='sr-only'>{$prevlabel}</span> </a></li>";

    if ($lastpage < 7 + ($adjacents * 2)){
        for ($counter = 1; $counter <= $lastpage; $counter++){
            if($lastpage != 1){
                if ($counter == $page){
                    $pagination.= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";

                } else{
                    $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }
        }
    } elseif($lastpage > 5 + ($adjacents * 2)){

        if($page < 1 + ($adjacents * 2)) {

            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                if ($counter == $page){
                    $pagination.= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }

            $pagination.= "<li class='dot page-item'>...</li>";
            $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$lpm1}'>{$lpm1}</a></li>";

        } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

            $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page=1'>1</a></li>";
            $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page=2'>2</a></li>";
            $pagination.= "<li class='dot page-item'>...</li>";
            for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                if ($counter == $page){
                    $pagination.= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";

                }
            }
            $pagination.= "<li class='dot page-item'>..</li>";
            $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$lpm1}'>{$lpm1}</a></li>";

        } else {

            $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page=1'>1</a></li>";
            $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page=2'>2</a></li>";
            $pagination.= "<li class='dot page-item'>..</li>";
            for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                if ($counter == $page){
                    $pagination.= "<li class='page-item active'><a class='page-link'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }
        }
    }

    if($page < $counter - 1){
        $pagination.= "<li class='page-item'><a class='page-link' href='{$url}page={$next}'><span aria-hidden='true'>»</span><span class='sr-only'>{$nextlabel}</span> </a></li>";
    }

    $pagination.= "</ul>";
    $pagination.= '</div>';
    $pagination.= "</div>";
    $pagination.= "</div>";
}


?>

<?php echo $pagination; ?>
@endif
