<h2 class="no-spacing">{$lang.projects}</h2>
<hr />
{loop="$projects"}
<div class="project">
    <img class="pull-left" src="{$settings.base_url}/content/project_imgs/{if="file_exists(CDIR . '/content/project_imgs/'. $value.id . '.png')"}{$value.id}{else}default.png{/if}" />
    <div class="pull-left">
        <h3 class="no-spacing"><a href="project/{$value.id}">{$value.name}</a> <small>({$value.short})</small></h3>
        {$value.description}<br /><br />
        {$lang.lead}: {$value.project_lead|userData:'displayname'}
    </div>
    <div class="clear"></div>
</div>
{/loop}
{if="count($projects) == 0"}
{$lang.no_projects}
{/if}