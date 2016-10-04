<?php if(!empty($emails_sent)): ?>

<div class="col-sm-12 col-md-12 col-lg-12">
    <div class="form-group">

        <table class="table table-striped">
        <thead>
            <tr role="row">
            <th>Envios</th>
            <th>Nombre</th>
            <th>Emails</th>
            <th>Registrado</th>
            <th>Reenvió</th>
			</tr>
        </thead>
        <tbody>
        <?php foreach($emails_sent as $row1): ?> 

            <tr class="gradeA odd" role="row">
                <td class="center"><?php echo (!empty($row1["number_of_times"])?$row1["number_of_times"]:"&nbsp"); ?> </td>
                <td class="center"><?php echo (!empty($row1["registred_name"])?$row1["registred_name"]:"&nbsp"); ?> </td>
                <td class="center"><?php echo (!empty($row1["emails"])?$row1["emails"]:"&nbsp"); ?> </td>
                <td class="center"><?php echo (!empty($row1["registred_on"])?$row1["registred_on"]:"&nbsp"); ?> </td>
                <td class="center"><?php echo (!empty($row1["updated_on"])?$row1["updated_on"]:"&nbsp"); ?> </td>
            </tr>

        <?php endforeach; ?>  


        </tbody>
        </table>			                    	
    </div> 
</div>
<?php endif; ?>