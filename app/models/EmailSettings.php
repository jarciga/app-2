<?php

class EmailSettings extends \Eloquent {
	protected $fillable = [];
	protected $table = 'email_settings';
	
    public function getTextEmail() {

		return EmailSettings::all();					  					  		

    }	
	
}