<div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
    
        <div class="row">
        <div class="col-sm-12">

            <table class="table table-striped table-bordered table-hover dataTable no-footer" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info">
            <thead>
                <tr role="row">
                <th>Id</th>
                <th >Nombre</th>
            </thead>
            <tbody>

            <?php if(!empty($records_array)): ?>  
            <?php foreach($records_array as $rows): ?> 

                <tr class="gradeA odd" role="row">
                    <td class="center"><?php echo (!empty($rows["id"])? anchor(base_url()."cinepixi/movie/movieView/".$rows["id"],$rows["id"]) :"&nbsp;") ?></td>
                    <td class="center"><?php echo (!empty($rows["name"])?$rows["name"]:"&nbsp;") ?></td>
                </tr>

            <?php endforeach; ?>  
            <?php endif; ?>  


            </tbody>
            </table>
        </div>
        </div>

    <div class="row">
            <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                <?php echo $pagination; ?>
            </div>
    </div>

</div>