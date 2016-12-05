<div class="outer forum_block_<{$block.disp_mode}>">
    <{if $block.disp_mode == 0}>
        <div class="head align_center">
            <div class="block_full_forum floatleft"><{$smarty.const._MB_NEWBB_FORUM}></div>
            <div class="block_full_topic floatleft"><{$smarty.const._MB_NEWBB_TOPIC}></div>
            <div class="block_full_reply floatleft"><{$smarty.const._MB_NEWBB_RPLS}></div>
            <div class="block_full_view floatleft"><{$smarty.const._MB_NEWBB_VIEWS}></div>
            <div class="_col_end"><{$smarty.const._MB_NEWBB_LPOST}></div>
            <div class="clear"></div>
        </div>

        <{foreachq item=topic from=$block.topics}>
            <div class="align_center <{cycle values="even,odd"}>">
                <div class="block_full_forum floatleft left"><a href="<{$topic.seo_forum_url}>"><{$topic.forum_name}></a></div>
                <div class="block_full_topic floatleft left"><a href="<{$topic.seo_topic_url}>"><{$topic.title}></a></div>
                <div class="block_full_reply floatleft"><{$topic.replies}></div>
                <div class="block_full_view floatleft"><{$topic.views}></div>
                <div class="_col_end right"><{$topic.time}><br /><{$topic.topic_poster}>&nbsp;<a href="<{$topic.seo_url}>"><{$topic.topic_page_jump}></a></div>
                <div class="clear"></div>
            </div>
        <{/foreach}>

    <{elseif $block.disp_mode == 1}>

        <div class="head align_center">
            <div class="block_compact_topic floatleft"><{$smarty.const._MB_NEWBB_TOPIC}></div>
            <div class="block_compact_reply floatleft"><{$smarty.const._MB_NEWBB_RPLS}></div>
            <div class="_col_end"><{$smarty.const._MB_NEWBB_LPOST}></div>
            <div class="clear"></div>
        </div>
        <{foreachq item=topic from=$block.topics}>
            <div class="<{cycle values="even,odd"}>">
                <div class="block_compact_topic floatleft left"><a href="<{$topic.seo_topic_url}>"><{$topic.title}></a></div>
                <div class="block_compact_reply floatleft left"><{$topic.replies}></div>
                <div class="_col_end right"><{$topic.time}><br /><{$topic.topic_poster}>&nbsp;<a href="<{$topic.seo_url}>"><{$topic.topic_page_jump}></a></div>
                <div class="clear"></div>
            </div>
        <{/foreach}>
    <{elseif $block.disp_mode == 2}>
        <{foreachq item=topic from=$block.topics}>
            <div class="<{cycle values="even,odd"}>">
                <div><a href="<{$topic.seo_url}>"><{$topic.title}></a></div>
                <div class="clear"></div>
            </div>
        <{/foreach}>
    <{/if}>
</div>
<div class="clear"></div>
<{if $block.indexNav}>
    <div class="pagenav">
        <a href="<{$block.seo_top_allposts}>"><{$smarty.const._MB_NEWBB_ALLPOSTS}></a> |
        <a href="<{$block.seo_top_alltopics}>"><{$smarty.const._MB_NEWBB_ALLTOPICS}></a> |
        <a href="<{$block.seo_top_allforums}>"><{$smarty.const._MB_NEWBB_VSTFRMS}></a>
    </div>
<{/if}>