@if(isset($data->pagination))
<?php
$current_page = $data->pagination->current_page;
$total_pages = $data->pagination->last_page;
$per_page = count($data->data) == 0 ? 1 : count($data->data);
$page = $current_page;

$total = $total_pages;
$adjacents = "2";
$firstlabel = "&laquo; ".trans('pagination.first');
$prevlabel = "&lsaquo; ".trans('pagination.prev');
$nextlabel = trans('pagination.next')." &rsaquo;";
$lastlabel = trans('pagination.last')." &raquo;";

$first = 1;
$page = ($page == 0 ? 1 : $page);
$start = ($page - 1) * $per_page;
$pageTitle = trans('main.show_records',['begin'=> $first, 'end' => $per_page, 'total' => $data->pagination->total_count]);
$pageTitles = trans('main.pages');
$prev = $page - 1;
$next = $page + 1;

//dd($_SERVER['QUERY_STRING']);
$paramsOld = strpos($_SERVER['REQUEST_URI'],'?') != false ? $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'].'?';
//dd($paramsOld);

// Showing 1 to 10 of 57 entries

$url = str_replace('&page='.$prev,'',$paramsOld);
$url = str_replace('?page='.$prev,'?',$url);
$url = str_replace('?page='.$page,'?',$url);
$url = str_replace('&page='.$page,'',$url);

$url .= \Request::getQueryString() != "" ? strpos($_SERVER['REQUEST_URI'],'?page=') != false ? '' : '&' : '';

$lastpage = $total_pages;

$lpm1 = $lastpage - 1;

$pagination = "";
if($lastpage >= 1){

    $pagination .= '<div class="row mb-5 mt-3 pagin">';
    $pagination .= "<div class='col-md-6 mt-1 d-none d-md-block'><p>{$pageTitle} </p> </div>";
    $pagination .= '<div class="col-md-6 dataTables_wrapper">';
    $pagination .= '<div class="float-right dataTables_paginate paging_simple_numbers" id="kt_datatable_paginate">';
    $pagination .= "<ul class='pagination'>";
    //$pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";
    $disabled = '';
    if($page === 1){
        $disabled = 'disabled';
    }
    if ($lpm1 >0){
        $pagination.= "<li class='page-item paginate_button pagination-rounded page-prev {$disabled}'><a class='page-link' href='{$url}page={$prev}'><span aria-hidden='true'> < </span><span class='sr-only'>{$prevlabel}</span> </a></li>";  
    } 

    if ($lastpage < 7 + ($adjacents * 2)){
        for ($counter = 1; $counter <= $lastpage; $counter++){
            if($lastpage != 1){
                if ($counter == $page){
                    $pagination.= "<li class='page-item paginate_button pagination-rounded active'><a class='page-link'>{$counter}</a></li>";

                } else{
                    $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }
        }
    } elseif($lastpage > 5 + ($adjacents * 2)){

        if($page < 1 + ($adjacents * 2)) {

            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                if ($counter == $page){
                    $pagination.= "<li class='page-item paginate_button pagination-rounded active'><a class='page-link'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }

            $pagination.= "<li class='dot page-item paginate_button pagination-rounded'>...</li>";
            $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page={$lpm1}'>{$lpm1}</a></li>";

        } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

            $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page=1'>1</a></li>";
            $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page=2'>2</a></li>";
            $pagination.= "<li class='dot page-item paginate_button pagination-rounded'>...</li>";
            for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                if ($counter == $page){
                    $pagination.= "<li class='page-item paginate_button pagination-rounded active'><a class='page-link'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";

                }
            }
            $pagination.= "<li class='dot page-item paginate_button pagination-rounded'>..</li>";
            $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page={$lpm1}'>{$lpm1}</a></li>";

        } else {

            $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page=1'>1</a></li>";
            $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page=2'>2</a></li>";
            $pagination.= "<li class='dot page-item paginate_button pagination-rounded'>..</li>";
            for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                if ($counter == $page){
                    $pagination.= "<li class='page-item paginate_button pagination-rounded active'><a class='page-link'>{$counter}</a></li>";
                } else{
                    $pagination.= "<li class='page-item paginate_button pagination-rounded'><a class='page-link' href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }
        }
    }

    if($page < $counter - 1){
        $pagination.= "<li class='page-item paginate_button pagination-rounded page-next'><a class='page-link' href='{$url}page={$next}'><span aria-hidden='true'> > </span><span class='sr-only'>{$nextlabel}</span> </a></li>";
    }

    $pagination.= "</ul>";
    $pagination.= '</div>';
    $pagination.= "</div>";
    $pagination.= "</div>";
}


?>

<?php echo $pagination; ?>
@endif
