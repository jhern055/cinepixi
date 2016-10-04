<div>
<div style="margin:0 2em;font-family:Trebuchet MS,Arial,sans-serif;line-height:140%;font-size:14px;color:#000066">

<table style="border:0;padding:0;margin:0;width:100%">
<tbody>

<tr>
<td width="1%">
<strong>Copia y pega el enlace que te enviamos en la barra del navegador o dale click</strong>
</td>
</tr>

</tbody>
</table>

<hr style="border:1px solid #ccc;padding:0;margin:0">
<ul style="clear:both;padding:0 0 0 1.2em;width:100%">

</ul>

<table>
<tbody>

<tr>
<td style="margin-bottom:0;line-height:1.4em">
<p style="font-size:14px;color:#555;margin:9px 0 3px 0;font-family:Trebuchet MS,Arial,sans-serif;line-height:140%;font-size:14px">
<span>Usuario:</span><?php echo (!empty($nickname)?$nickname:""); ?>
</p>

<div style="margin:0;font-family:Trebuchet MS,Arial,sans-serif;line-height:140%;font-size:14px;color:#000066">

<fieldset>
<legend><span style="font-family:trebuchet ms">link</span></legend>
<div style="text-align:justify">
    <span style="font-family:trebuchet ms">
        <?php echo base_url()."login/change_passwrd/$pass_tmp/".encode_url($id); ?>
    </span>
</div>
</fieldset>

</div>

</td>
</tr>

<tr>

</tbody>
</table>


</div   >
</div>
