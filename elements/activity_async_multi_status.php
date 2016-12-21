<?php if(isset($unfilled_pyramids)):?>
    <?php if(count($unfilled_pyramids)):?>
        <div id="activity-pyramid-state-block">
            <div id="activity-pyramid-state-block-title"><?=TS("Pyramid state:")?></div>
            <ul class="activity-pyramid-state-block-pyramid-list">
                <li class="activity-pyramid-state-block-pyramid-list-item">Submission will start at <?=date("l jS G:i", $last_expired_timestamp)?></li>
                <?php foreach($unfilled_pyramids as $pkey => $unfilled_pyramid):?>
                    <li class="activity-pyramid-state-block-pyramid-list-item">Pyramid <?=($unfilled_pyramid['pid'] + 1)?> remaining slots <?=($unfilled_pyramid['slots'])?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else:?>
        <div id="activity-pyramid-state-block">
            <ul class="activity-pyramid-state-block-pyramid-list">
                <?php if(!(empty($available_students) and $npid === null)):?>
                    <li class="activity-pyramid-state-block-pyramid-list-item">Submission started at <?=date("l jS G:i", $last_expired_timestamp)?></li>
                    <li class="activity-pyramid-state-block-pyramid-list-item">Submission will end at <?=date("l jS G:i", $question_submit_expiry_timestamp)?></li>
                    <li class="activity-pyramid-state-block-pyramid-list-item">Available students <?=implode(', ', $available_students)?></li>
                <?php else:?>
                    <li>Waiting for the first student.</li>
                <?php endif;?>
            </ul>
        </div>
    <?php endif;?>
<?php endif;?>