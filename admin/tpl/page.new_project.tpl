<h3 class="no-spacing">{$lang.admin.new_project} <a href="projects" class="btn btn-danger pull-right">{$lang.back}</a></h3>
<hr />
<form class="half-width form-horizontal" method="post" action="{$settings.base_url}/admin/new_project">
    <div class="form-group">
        <label class="col-sm-4 control-label">{$lang.project_fields.name}</label>
        <div class="col-sm-6">
            <input class="form-control" name="name" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">{$lang.project_fields.short}</label>
        <div class="col-sm-6">
            <input class="form-control" name="short" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">{$lang.project_fields.description}</label>
        <div class="col-sm-6">
            <textarea class="form-control" name="description"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">{$lang.project_fields.upload_picture}</label>
        <div class="col-sm-6">
            <input type="file" name="file" accept="image/*" onchange="updateImage( this, event )" />
            <img class="hidden full-width auto-height form-control" id="imgpreview" src="" />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-6">
            <input type="submit" class="btn btn-success" value="{$lang.save}">            
        </div>
    </div>
</form>
<script>
    $( 'input[name=file]' ).change( function( $event )
    {
        $( '#imgpreview' ).removeClass( 'hidden' ).attr( 'src', URL.createObjectURL( $event.target.files[0] ) );
    } );
</script>