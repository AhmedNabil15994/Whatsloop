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
    $pagination .= "<div class='kt-pagination kt-pagination--brand'>";
    $pagination .= "<ul class='kt-pagination__links'>";
    $pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";

    if ($page > 1) $pagination.= "<li class='kt-pagination__link--first'><a href='{$url}page={$first}'><i class='fa fa-angle-double-left kt-font-brand'></i> {$firstlabel}</a></li>";
    if ($page > 1) $pagination.= "<li class='kt-pagination__link--prev'><a href='{$url}page={$prev}'><i class='fa fa-angle-right kt-font-brand'></i> {$prevlabel}</a></li>";

    if ($lastpage < 7 + ($adjacents * 2)){
        for ($counter = 1; $counter <= $lastpage; $counter++){
            if ($counter == $page){
                $pagination.= "<li><a class='current'>{$counter}</a></li>";

            } else{
                $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";
            }
        }
    } elseif($lastpage > 5 + ($adjacents * 2)){

        if($page < 1 + ($adjacents * 2)) {

            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                if ($counter == $page){
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }

            $pagination.= "<li class='dot'>...</li>";
            $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
            $pagination.= "<li class='kt-pagination__link--las'><a href='{$url}page={$lastpage}'><i class='fa fa-angle-double-right kt-font-brand'> {$lastpage}</a></li>";

        } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

            $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
            $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
            $pagination.= "<li class='dot'>...</li>";
            for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                if ($counter == $page){
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";

                }
            }
            $pagination.= "<li class='dot'>..</li>";
            $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
            $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";

        } else {

            $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
            $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
            $pagination.= "<li class='dot'>..</li>";
            for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                if ($counter == $page){
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }
        }
    }

    if ($page < $counter - 1) {
        $pagination.= "<li class='kt-pagination__link--next'><a href='{$url}page={$next}'><i class='fa fa-angle-left kt-font-brand'></i> {$nextlabel}</a></li>";
        $pagination.= "<li><a href='{$url}page=$lastpage'>{$lastlabel}</a></li>";
    }

    $pagination.= "</ul>";
    $pagination.= '<div class="kt-pagination__toolbar">';
    $pagination.= '<select class="form-control kt-font-brand" style="width: 60px">';
    $pagination.= '<option value="10">10</option>';
    $pagination.= '<option value="20">20</option>';
    $pagination.= '<option value="30">30</option>';
    $pagination.= '<option value="50">50</option>';
    $pagination.= '<option value="100">100</option>';
    $pagination.= '</select>';
    $pagination.= '<span class="pagination__desc">';
    $pagination.= 'Displaying '.$data->pagination->count.' of '.$data->pagination->total_count.' records';
    $pagination.= '</span>';
    $pagination.= '</div>';
    $pagination.= "</div>";
}


?>

<?php echo $pagination; ?>
@endif
