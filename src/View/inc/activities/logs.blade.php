<div class="panel p-4 clearfix">
    <ul class="timeline timeline-inverse" id="ul_timeline_activies"></ul>
</div>
@push('after_scripts')
    <script>
        $(document).ready(function () {
            load_activity();
            $('#ul_timeline_activies').on('click','button.crm-load-more',function(){
                load_activity($(this).attr('page-number'));
            });
        });
        function load_activity(page = 1) {
            $.ajax({
                url:"{{ url(config('stlc.stlc_route_prefix', 'developer').'/activities') }}",
                type:"Post",
                data:{context_type:<?php echo json_encode(get_class($item)); ?>,'context_id':'{{$item->id}}','page':page},
                beforeSend: function() {
                    $('#ul_timeline_activies').append(`<div class="fa-5x text-center crm-spin">
                        <i class="fa fa-spinner fa-spin"></i></div>`);
                },
                success:function(data) {
                    if(data.status == "success") {
                        var html = $(data.html);
                        var current_page = parseInt(data.data.current_page);
                        var last_page = parseInt(data.data.last_page);
                        $('.crm-spin,.crm-load-more,.fa.fa-clock.bg-gray:last').remove();
                        // console.log(last_page +" - "+ current_page);
                        if(last_page > current_page) {
                            $(html).find('button.crm-load-more').attr('page-number',(current_page+1));
                        } else {
                            $(html).find('button.crm-load-more').addClass('hide');
                        }
                        if(page == 1) {
                            $('#ul_timeline_activies').html(html);
                        } else {
                            $('#ul_timeline_activies').append(html);
                        }
                    } else {
                        $('#ul_timeline_activies').html('<h1 class="text-center">data not found</h1>');
                        console.log(data);
                    }
                }
            });
        }
    </script>
@endpush