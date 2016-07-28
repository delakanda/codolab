<?php

begin()
    
    ->schema('workflow')
        
        ->table('actions')
            ->column('action_id')->type('integer')->nulls(false)
            ->column('name')->type('string')
            ->column('destination_status_id')->type('integer')
            ->column('status_id')->type('integer')
            ->column('action_link')->type('string')
            ->primaryKey('action_id')->name('action_id_pkey')
            ->autoIncrement()

        ->table('status')
            ->column('status_id')->type('integer')->nulls(false)
            ->column('name')->type('string')
            ->column('request_id')->type('integer')
            ->primaryKey('status_id')->name('status_id_pkey')
            ->autoIncrement()

        ->table('issues')
            ->column('issue_id')->type('integer')->nulls(false)
            ->column('title')->type('string')
            ->column('extra_data')->type('text')
            ->column('status_id')->type('integer')
            ->primaryKey('issue_id')->name('issue_id_pkey')
            ->autoIncrement()

        ->table('issue_history')
            ->column('issue_history_id')->type('integer')->nulls(false)
            ->column('time')->type('timestamp')
            ->column('issue_id')->type('integer')
            ->column('status_id')->type('integer')
            ->column('comment')->type('string')
            ->column('user_id')->type('integer')
            ->primaryKey('issue_history_id')->name('issue_history_id_pkey')
            ->autoIncrement()
            ->index('issue_id')->name('fki_issue_history_issue_id_fkey')

        ->table('issue_history_attachments')
            ->column('issue_history_attachment_id')->type('integer')->nulls(false)
            ->column('issue_history_id')->type('integer')
            ->column('description')->type('string')
            ->column('object_id')->type('integer')
            ->primaryKey('issue_history_attachment_id')->name('issue_history_attachment_id_pkey')
            ->autoIncrement()

         ->table('comment_mentions')
            ->column('comment_mention_id')->type('integer')->nulls(false)
            ->column('dismissed')->type('boolean')->nulls(true)->defaultValue("false")
            ->column('issue_history_id')->type('integer')->nulls(true)
            ->column('user_id')->type('integer')->nulls(true)
            ->primaryKey('comment_mention_id')->name('comment_mention_id')
            ->autoIncrement()

        ->table('status_users')
            ->column('status_user_id')->type('integer')->nulls(false)
            ->column('status_id')->type('integer')->nulls(false)
            ->column('user_id')->type('integer')->nulls(false)
            ->primaryKey('status_user_id')->name('status_user_id_pkey')
            ->autoIncrement()

        ->table('transcripts')
            ->column('transcript_id')->type('integer')->nulls(false)
            ->column('issue_id')->type('integer')
            ->column('details')->type('text')
            ->primaryKey('transcript_id')->name('transcript_id_pkey')
            ->autoIncrement()

        ->table('requests')
            ->column('request_id')->type('integer')->nulls(false)
            ->column('name')->type('string')->nulls(false)->length(1024)
            ->column('code')->type('string')->nulls(false)->length(64)
            ->column('colour')->type('string')->nulls(true)->length(32)
            ->primaryKey('request_id')->name('request_id_pkey')
            ->autoIncrement()
            ->unique('name')->name('requests_name_ukey')
            ->unique('code')->name('requests_code_ukey')
        
        ->view('issues_view')->definition("select issues.*,
            status.name as status,
            status.request_id,
            requests.name as request,
            requests.code,
            requests.colour	
            from issues, requests, status 
            where issues.status_id = status.status_id and status.request_id = requests.request_id;")

->end();