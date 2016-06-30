<div class='job-queue-status-box job-queue-{$status}'>
    <h2>{$status}</h2>
    <p>
        <div id='message'>{$message}</div>
        {if $progress neq ''}
        <div id='progress'>{$progress} % completed</div>
        {/if}
    </p>
    <p>
        <a class='button' href='{$path}/clear'>&nbsp;&nbsp;&nbsp;{$reset}&nbsp;&nbsp;&nbsp;</a>
    </p>
</div>