<?php
    Route::post('check-unique',['as'=>'check-unique','uses'=>'Tools@postCheckUnique']);
    Route::post('change-language',['as'=>'change-language','uses'=>'Tools@postChangeLanguage']);
	Route::post('change-odds-type',['as'=>'change-odds-type','uses'=>'Tools@postChangeOddsType']);
	/**
     * ======================================================================================================
     *      Frontend routing start from here
     * ======================================================================================================
     */
	  Route::get('/test','Tools@showSession');

    Route::get('/user-authentication/{token}', [
        'as' => 'user-authentication',
        'uses' => 'ApiIntegration\ProviderApi@postAuthentication',
    ]);

    Route::post('/bet-request', [
        'as' => 'bet-request',
        'uses' => 'ApiIntegration\ProviderApi@postBetRequest',
    ]);

    Route::get('/user-host', [
        'as' => 'user-host',
        'uses' => 'ApiIntegration\ProviderApi@test',
    ]);

    Route::get('/returnBetAmount', [
        'as' => 'returnBetAmount',
        'uses' => 'ApiIntegration\ProviderApi@returnBetAmount',
    ]);

    Route::get('/fetch-extra-odds',['as'=>'fetch-extra-odds','uses'=>'Frontend\OddsListing@fetchExtraOdds']);

    Route::group(['middleware'=>['web','lang'],'as'=> 'front-','namespace'=>'Frontend'],function(){

        Route::get('/',['as'=>'home','uses'=>'Landing@index']);
        Route::post('ajax/post-home-match',['as'=>'post-home-match','uses'=>'Landing@postHomeMatch']);
        Route::get('view/{slug}',['as'=>'cms','uses'=>'Landing@getView']);
        Route::get('no-data-found}',['as'=>'no-data-found','uses'=>'Landing@getNoDataFound']);
        // Route::get('registration',['as'=>'get-registration','uses'=>'UserAuthentication@index']);
        // Route::post('registration',['as'=>'post-registration','uses'=>'UserAuthentication@postRegistration']);
        // Route::get('login',['as'=>'get-login','uses'=>'UserAuthentication@getLogin']);
        // Route::post('login',['as'=>'post-login','uses'=>'UserAuthentication@postLogin']);
        Route::post('logout',['as'=>'post-logout','uses'=>'UserAuthentication@postLogOut']);
        Route::get('active-my-account/{token}',['as'=>'active-account','uses'=>'UserAuthentication@getActiveMyAccount']);
        Route::post('send-forgot-password-link',['as'=>'send-forgot-password-link','uses'=>'UserAuthentication@sendForgotPasswordLink']);
		Route::get('reset-password/{token}',['as'=>'reset-password','uses'=>'UserAuthentication@getResetPassword']);
		Route::post('reset-password',['as'=>'post-reset-password','uses'=>'UserAuthentication@postResetPassword']);

		Route::group(['middleware' => 'front.auth'],function(){
          Route::get('profile',['as'=>'get-profile','uses'=>'UserProfileSettings@index']);
          Route::post('profile',['as'=>'post-profile','uses'=>'UserProfileSettings@postUpdateProfile']);
     	    Route::get('change-password',['as'=>'get-change-password','uses'=>'UserProfileSettings@getChangePassword']);
          Route::post('change-password',['as'=>'post-change-password','uses'=>'UserProfileSettings@postChangePassword']);
          Route::get('transactions',['as'=>'get-transactions','uses'=>'UserProfileSettings@getTransactionHistory']);
          Route::post('ajax/search-transactions',['as'=>'search-transactions','uses'=>'UserProfileSettings@postSearchTransactionHistory']);
          Route::post('ajax/search-transactions-paginate',['as'=>'search-transactions-paginate','uses'=>'UserProfileSettings@postSearchPaginateTransactionHistory']);
		      Route::post('wallet-balance',['as'=>'post-wallet-balance','uses'=>'UserProfileSettings@getWalletBalance']);


            Route::group(['prefix' => 'history-bet','as' => 'history-bet-'],function(){
                  Route::get('single',['as' => 'single','uses' => 'SingleHistoryBet@getAllBets']);
                  Route::post('getBetSlipDetails',['as' => 'getBetSlipDetails','uses' => 'SingleHistoryBet@getBetsDetails']);
                  Route::post('searchBetsSingle',['as' => 'searchBetsSingle','uses' => 'SingleHistoryBet@searchBetsSingle']);
                  Route::get('combo',['as' => 'combo','uses' => 'ComboHistoryBet@getAllBets']);
                  Route::post('getComboBetSlips',['as' => 'getComboBetSlips','uses' => 'ComboHistoryBet@getComboBetSlips']);
                  Route::post('getComboBetSlipsSearch',['as' => 'getComboBetSlipsSearch','uses' => 'ComboHistoryBet@getComboBetSlipsSearch']);
                  Route::post('getComboBetSlipDetails',['as' => 'getComboBetSlipDetails','uses' => 'ComboHistoryBet@getBetsDetails']);
                  Route::post('searchBetsCombo',['as' => 'searchBetsCombo','uses' => 'ComboHistoryBet@searchBetsCombo']);
            });
		 });

         Route::group(['middleware' => 'front.auth'],function()
         {
              Route::get('support-ticket',['as'=>'get-support-ticket','uses'=>'SupportTicket@getSupportTicket']);
     		      Route::post('support-ticket',['as'=>'post-support-ticket','uses'=>'SupportTicket@postSupportTicket']);
     		      Route::get('show-ticket-details/{id?}',['as'=>'show-ticket-details','uses'=>'SupportTicket@getTicketDetails']);
     		      Route::post('ticket-reply-player',['as'=>'ticket-reply-player','uses'=>'SupportTicket@postTicketReply']);
     		      Route::post('change-status',['as'=>'change-status','uses'=>'SupportTicket@postChangeStatus']);
              Route::post('unread-ticket-reply',['as'=>'unread-ticket-reply','uses'=>'SupportTicket@getUnreadSupportTticketReply']);
         });

		 Route::group(['prefix' => 'sports', 'as' => 'sports-'],function(){
       Route::get('/',['as'=>'get-Pre-Match-Betting','uses'=>'LeagueListing@index']);
			 Route::get('/{sport_slug}',['as'=>'get-all-league','uses'=>'LeagueListing@getAllLeague']);
			 Route::post('/{sport_slug}/show-matches',['as'=>'post-show-matches','uses'=>'MatchListing@postShowMatches']);
			 Route::get('/{sport_slug}/{country_slug}',['as'=>'get-league-by-country','uses'=>'LeagueListing@getLeagueByCountry']);
			 Route::post('ajax/league-by-time',['as'=>'get-league-by-time','uses'=>'LeagueListing@postLeagueByTime']);
			 Route::post('ajax/fetch-extra-odds',['as'=>'fetch-extra-odds','uses'=>'OddsListing@fetchExtraOdds']);
		 });
		 Route::group(['prefix' => 'prematch-betting', 'as' => 'prematch-betting-'],function(){
             Route::post('place-bet',['as'=>'place-bet','uses'=>'PrematchBetting@placeBetIntoSlip']);
			 Route::post('remove-bet',['as'=>'remove-bet','uses'=>'PrematchBetting@removeBetFronSlip']);
			 Route::post('save-single-bet',['as'=>'save-single-bet','uses'=>'PrematchBetting@saveSingleBet']);
			 Route::post('save-combo-bet',['as'=>'save-combo-bet','uses'=>'PrematchBetting@saveComboBet']);
			 Route::get('bet-slip',['as'=>'bet-slip','uses'=>'PrematchBetting@getBetSlip']);
		 });
         Route::group(['prefix' => 'live-betting', 'as' => 'live-betting-'],function(){
             Route::post('place-bet',['as'=>'place-bet','uses'=>'LiveBetting@placeLiveBetIntoSlip']);
         });
    });
/**
 * ======================================================================================================
 * 					Admin routing start from here
 * ======================================================================================================
 */
Route::get(ADMIN_PATH,['as'=>'admin','uses'=>'admin\Authentication@getLogin']);
Route::get(ADMIN_PATH.'/login',['as'=>'admin-login','uses'=>'admin\Authentication@getLogin']);
Route::post(ADMIN_PATH.'/login',['as'=>'admin-post-login','uses'=>'admin\Authentication@postLogin']);
Route::get(ADMIN_PATH.'/forgot-password',['as'=>'admin-get-forgot-password','uses'=>'admin\Authentication@getForgotPassword']);
Route::post(ADMIN_PATH.'/forgot-password',['as'=>'admin-post-forgot-password','uses'=>'admin\Authentication@postForgotPassword']);

Route::get(ADMIN_PATH.'/reset-password/{unique_code}',['as'=>'admin-get-reset-password','uses'=>'admin\Authentication@getResetPassword']);
Route::post(ADMIN_PATH.'/reset-password',['as'=>'admin-post-reset-password','uses'=>'admin\Authentication@postResetPassword']);

Route::group(['prefix'=> ADMIN_PATH,'as' => 'admin-','middleware'=>'admin.auth','lang','namespace'=>'admin'],function(){


  Route::group(['as' => 'user-group-','prefix' => 'user-group','middleware'=>'admin.auth:user-group-'],function(){
    Route::get('/',['uses'=>'RoleCrud@show','middleware' => 'admin.auth:user-group-@canView']);
    Route::get('list',['as'=>'list','uses'=>'RoleCrud@show','middleware' => 'admin.auth:user-group-@canView']);
    Route::get('view/{id?}',['as'=>'view','uses'=>'RoleCrud@view','middleware' => 'admin.auth:user-group-@canView']);
    Route::get('add',['as'=>'add','uses'=>'RoleCrud@add','middleware' => 'admin.auth:user-group-@canAdd']);
    Route::post('insert',['as'=>'insert','uses'=>'RoleCrud@insert','middleware' => 'admin.auth:user-group-@canAdd']);
    Route::get('edit/{id?}',['as'=>'edit','uses'=>'RoleCrud@edit','middleware' => 'admin.auth:user-group-@canModify']);
    Route::post('update',['as'=>'update','uses'=>'RoleCrud@update','middleware' => 'admin.auth:user-group-@canModify']);
    Route::get('delete/{id?}',['as'=>'delete','uses'=>'RoleCrud@delete','middleware' => 'admin.auth:user-group-@canModify']);
  });



    Route::group(['prefix'=> 'sub-agent-management','as' => 'sub-agent-management-','middleware'=>'admin.auth:sub-agent-management'],function(){
                Route::group(['as' => 'sub-agent-','prefix' => 'sub-agent','middleware'=>'admin.auth:sub-agent-list'],function(){
                    Route::get('list',['as'=>'list','uses'=>'SubAgentCrud@show','middleware' => 'admin.auth:sub-agent-list@canView']);
                    Route::get('view/{id?}',['as'=>'view','uses'=>'SubAgentCrud@view','middleware' => 'admin.auth:sub-agent-list@canView']);
                    Route::get('edit/{id?}',['as'=>'edit','uses'=>'SubAgentCrud@EditSubAgent','middleware' => 'admin.auth:sub-agent-list@canModify']);
                    Route::post('update',['as'=>'update','uses'=>'SubAgentCrud@PostEditSubAgent','middleware' => 'admin.auth:sub-agent-list@canModify']);
                    Route::get('delete/{id?}',['as'=>'delete','uses'=>'SubAgentCrud@DeleteSubAgent','middleware' => 'admin.auth:sub-agent-list@canModify']);

                });
                Route::get('sub-agent-add',['as'=>'sub-agent-add','uses'=>'SubAgentCrud@getSubAgentAdd','middleware' => 'admin.auth:sub-agent-management@canAdd']);
                Route::post('sub-agent-add',['as'=>'sub-agent-add','uses'=>'SubAgentCrud@postSubAgentAdd','middleware' => 'admin.auth:sub-agent-management@canAdd']);
                Route::post('all-agents',['as'=>'all-agents','uses'=>'SubAgentCrud@AllAgents']);
        });

           Route::group(['prefix'=> 'player-management','as' => 'player-management-','middleware'=>'admin.auth:player-management'],function(){
             Route::group(['prefix'=> 'player','as' => 'player-','middleware'=>'admin.auth:player-list'],function(){
             Route::get('list',['as'=>'list','uses'=>'PlayerCrud@show','middleware' => 'admin.auth:player-list@canView']);
             Route::get('view/{id?}',['as'=>'view','uses'=>'PlayerCrud@view','middleware' => 'admin.auth:player-list@canView']);
             Route::get('edit/{id?}',['as'=>'edit','uses'=>'PlayerCrud@EditPlayer','middleware' => 'admin.auth:player-list@canModify']);
             Route::post('update',['as'=>'update','uses'=>'PlayerCrud@PostEditPlayer','middleware' => 'admin.auth:player-list@canModify']);
             Route::get('delete/{id?}',['as'=>'delete','uses'=>'PlayerCrud@DeletePlayer','middleware' => 'admin.auth:player-list@canModify']);
           });
             Route::get('add-player',['as'=>'add-player','uses'=>'PlayerCrud@getAddUser','middleware' => 'admin.auth:player-list@canAdd']);
             Route::post('add-player',['as'=>'add-player','uses'=>'PlayerCrud@postAddUser','middleware' => 'admin.auth:player-list@canAdd']);
             Route::get('players-activity',['as'=>'players-activity','uses'=>'Registration@getPlayersActivity','middleware' => 'admin.auth:players-activity@canView']);
        });

        Route::group(['as' => 'wallet-management-','prefix' => 'user-wallet','middleware' => 'admin.auth:wallet-management-list@canView'],function(){
        Route::get('list',['as'=>'list','uses'=>'UserWalletCrud@show','middleware' => 'admin.auth:wallet-management-list@canView']);
        Route::get('view/{id?}',['as'=>'view','uses'=>'UserWalletCrud@view','middleware' => 'admin.auth:wallet-management-list@canView']);
        Route::get('add',['as'=>'add','uses'=>'UserWalletCrud@add','middleware' => 'admin.auth:wallet-management-list@canAdd']);
        Route::post('insert',['as'=>'insert','uses'=>'UserWalletCrud@insert','middleware' => 'admin.auth:wallet-management-list@canAdd']);
        Route::get('edit/{id?}',['as'=>'edit','uses'=>'UserWalletCrud@edit','middleware' => 'admin.auth:wallet-management-list@canModify']);
        Route::post('update',['as'=>'update','uses'=>'UserWalletCrud@update','middleware' => 'admin.auth:wallet-management-list@canModify']);
        Route::get('delete/{id?}',['as'=>'delete','uses'=>'UserWalletCrud@delete','middleware' => 'admin.auth:wallet-management-list@canModify']);
           });

        Route::group(['prefix'=> 'sports-book-management','as' => 'sports-book-management-','middleware'=>'admin.auth:sports-book-management'],function(){
	        Route::get('money-line',['as'=>'money-line','uses'=>'Registration@getMoneyLine','middleware' => 'admin.auth:money-line@canView']);
            Route::get('stake-limitation',['as'=>'stake-limitation','uses'=>'Registration@getStakeLimitation','middleware' => 'admin.auth:stake-limitation@canView']);

            Route::group(['as' => 'odds-editing-','prefix' => 'odds-editing','middleware'=>'admin.auth:odds-editing-'],function(){
                  Route::get('/',['uses'=>'OddsEditingCrud@show','middleware' => 'admin.auth:odds-editing-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'OddsEditingCrud@show','middleware' => 'admin.auth:odds-editing-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'OddsEditingCrud@view','middleware' => 'admin.auth:odds-editing-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'OddsEditingCrud@add','middleware' => 'admin.auth:odds-editing-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'OddsEditingCrud@insert','middleware' => 'admin.auth:odds-editing-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'OddsEditingCrud@edit','middleware' => 'admin.auth:odds-editing-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'OddsEditingCrud@update','middleware' => 'admin.auth:odds-editing-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'OddsEditingCrud@delete','middleware' => 'admin.auth:odds-editing-@canModify']);
            		});


            Route::group(['as' => 'league-settings-','prefix' => 'league','middleware'=>'admin.auth:league-settings-'],function(){
                  Route::get('/',['uses'=>'LeagueCrud@show','middleware' => 'admin.auth:league-settings-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'LeagueCrud@show','middleware' => 'admin.auth:league-settings-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'LeagueCrud@view','middleware' => 'admin.auth:league-settings-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'LeagueCrud@add','middleware' => 'admin.auth:league-settings-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'LeagueCrud@insert','middleware' => 'admin.auth:league-settings-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'LeagueCrud@edit','middleware' => 'admin.auth:league-settings-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'LeagueCrud@update','middleware' => 'admin.auth:league-settings-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'LeagueCrud@delete','middleware' => 'admin.auth:league-settings-@canModify']);
                  Route::get('get-all-leagues',['as'=>'get-all-leagues','uses'=>'LeagueCrud@getAllLegues','middleware' => 'admin.auth:league-settings-@canView']);
                });

            Route::group(['as' => 'bet-settings-','prefix' => 'bet-settings'],function(){
            			Route::get('list',['as'=>'list','uses'=>'BetSettingManagement@show','middleware' => 'admin.auth:bet-settings-list@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'BetSettingManagement@view','middleware' => 'admin.auth:bet-settings-list@canView']);
            			Route::get('add',['as'=>'add','uses'=>'BetSettingManagement@add','middleware' => 'admin.auth:bet-settings-list@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'BetSettingManagement@insert','middleware' => 'admin.auth:bet-settings-list@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'BetSettingManagement@edit','middleware' => 'admin.auth:bet-settings-list@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'BetSettingManagement@update','middleware' => 'admin.auth:bet-settings-list@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'BetSettingManagement@delete','middleware' => 'admin.auth:bet-settings-list@canModify']);
            		});

            Route::group(['as' => 'bookmakers-','prefix' => 'bookmaker','middleware'=>'admin.auth:bookmakers-'],function(){
          			Route::get('/',['uses'=>'BookmakerCrud@show','middleware' => 'admin.auth:bookmakers-@canView']);
          			Route::get('list',['as'=>'list','uses'=>'BookmakerCrud@show','middleware' => 'admin.auth:bookmakers-@canView']);
          			Route::get('view/{id?}',['as'=>'view','uses'=>'BookmakerCrud@view','middleware' => 'admin.auth:bookmakers-@canView']);
          			Route::get('add',['as'=>'add','uses'=>'BookmakerCrud@add','middleware' => 'admin.auth:bookmakers-@canAdd']);
          			Route::post('insert',['as'=>'insert','uses'=>'BookmakerCrud@insert','middleware' => 'admin.auth:bookmakers-@canAdd']);
          			Route::get('edit/{id?}',['as'=>'edit','uses'=>'BookmakerCrud@edit','middleware' => 'admin.auth:bookmakers-@canModify']);
          			Route::post('update',['as'=>'update','uses'=>'BookmakerCrud@update','middleware' => 'admin.auth:bookmakers-@canModify']);
          			Route::get('delete/{id?}',['as'=>'delete','uses'=>'BookmakerCrud@delete','middleware' => 'admin.auth:bookmakers-@canModify']);
          		});

            Route::group(['as' => 'sports-','prefix' => 'sport','middleware'=>'admin.auth:sports-'],function(){
              Route::get('/',['uses'=>'SportCrud@show','middleware' => 'admin.auth:sports-@canView']);
        			Route::get('list',['as'=>'list','uses'=>'SportCrud@show','middleware' => 'admin.auth:sports-@canView']);
        			Route::get('view/{id?}',['as'=>'view','uses'=>'SportCrud@view','middleware' => 'admin.auth:sports-@canView']);
        			Route::get('add',['as'=>'add','uses'=>'SportCrud@add','middleware' => 'admin.auth:sports-@canAdd']);
        			Route::post('insert',['as'=>'insert','uses'=>'SportCrud@insert','middleware' => 'admin.auth:sports-@canAdd']);
        			Route::get('edit/{id?}',['as'=>'edit','uses'=>'SportCrud@edit','middleware' => 'admin.auth:sports-@canModify']);
        			Route::post('update',['as'=>'update','uses'=>'SportCrud@update','middleware' => 'admin.auth:sports-@canModify']);
        			Route::get('delete/{id?}',['as'=>'delete','uses'=>'SportCrud@delete','middleware' => 'admin.auth:sports-@canModify']);
        		});

            Route::get('matches',['as'=>'matches','uses'=>'MatchManagement@index','middleware' => 'admin.auth:matches@canView']);
            Route::post('post-matches',['as'=>'post-matches','uses'=>'MatchManagement@postMatches','middleware' => 'admin.auth:matches@canView']);
            Route::post('post-change-match-status',['as'=>'post-change-match-status','uses'=>'MatchManagement@postChangeMatchStatus','middleware' => 'admin.auth:matches@canModify']);


            Route::group(['as' => 'market-','prefix' => 'market','middleware'=>'admin.auth:market-'],function(){
                  Route::get('/',['uses'=>'MarketCrud@show','middleware' => 'admin.auth:market-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'MarketCrud@show','middleware' => 'admin.auth:market-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'MarketCrud@view','middleware' => 'admin.auth:market-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'MarketCrud@add','middleware' => 'admin.auth:market-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'MarketCrud@insert','middleware' => 'admin.auth:market-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'MarketCrud@edit','middleware' => 'admin.auth:market-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'MarketCrud@update','middleware' => 'admin.auth:market-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'MarketCrud@delete','middleware' => 'admin.auth:market-@canModify']);
                  Route::get('get-all-markets',['as'=>'get-all-markets','uses'=>'MarketCrud@getAllMarkets','middleware' => 'admin.auth:market-@canView']);
                });

            Route::group(['as' => 'market-groups-','prefix' => 'Market-Group','middleware'=>'admin.auth:market-groups-'],function(){
                  Route::get('/',['uses'=>'MarketGroupCrud@show','middleware' => 'admin.auth:market-groups-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'MarketGroupCrud@show','middleware' => 'admin.auth:market-groups-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'MarketGroupCrud@view','middleware' => 'admin.auth:market-groups-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'MarketGroupCrud@add','middleware' => 'admin.auth:market-groups-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'MarketGroupCrud@insert','middleware' => 'admin.auth:market-groups-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'MarketGroupCrud@edit','middleware' => 'admin.auth:market-groups-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'MarketGroupCrud@update','middleware' => 'admin.auth:market-groups-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'MarketGroupCrud@delete','middleware' => 'admin.auth:market-groups-@canModify']);
            		});
        });

        Route::group(['prefix'=> 'content-management','as' => 'content-management-','middleware'=>'admin.auth:content-management'],function(){
            Route::group(['as' => 'cms-page-','prefix' => 'cms_page'],function(){
          			Route::get('list',['as'=>'list','uses'=>'CmsPageCrud@show','middleware' => 'admin.auth:cms-page-list@canView']);
          			Route::get('view/{id?}',['as'=>'view','uses'=>'CmsPageCrud@view','middleware' => 'admin.auth:cms-page-list@canView']);
          			Route::get('add',['as'=>'add','uses'=>'CmsPageCrud@add','middleware' => 'admin.auth:cms-page-list@canAdd']);
          			Route::post('insert',['as'=>'insert','uses'=>'CmsPageCrud@insert','middleware' => 'admin.auth:cms-page-list@canAdd']);
          			Route::get('edit/{id?}',['as'=>'edit','uses'=>'CmsPageCrud@edit','middleware' => 'admin.auth:cms-page-list@canModify']);
          			Route::post('update',['as'=>'update','uses'=>'CmsPageCrud@update','middleware' => 'admin.auth:cms-page-list@canModify']);
          			Route::get('delete/{id?}',['as'=>'delete','uses'=>'CmsPageCrud@delete','middleware' => 'admin.auth:cms-page-list@canModify']);
          	});

            Route::group(['as' => 'banner-image-','prefix' => 'banner_image'],function(){
                Route::get('list',['as'=>'list','uses'=>'BannerImageCrud@show','middleware' => 'admin.auth:banner-image-list@canView']);
                Route::get('view/{id?}',['as'=>'view','uses'=>'BannerImageCrud@view','middleware' => 'admin.auth:banner-image-list@canView']);
                Route::get('add',['as'=>'add','uses'=>'BannerImageCrud@add','middleware' => 'admin.auth:banner-image-list@canAdd']);
                Route::post('insert',['as'=>'insert','uses'=>'BannerImageCrud@insert','middleware' => 'admin.auth:banner-image-list@canAdd']);
                Route::get('edit/{id?}',['as'=>'edit','uses'=>'BannerImageCrud@edit','middleware' => 'admin.auth:banner-image-list@canModify']);
                Route::post('update',['as'=>'update','uses'=>'BannerImageCrud@update','middleware' => 'admin.auth:banner-image-list@canModify']);
                Route::get('delete/{id?}',['as'=>'delete','uses'=>'BannerImageCrud@delete','middleware' => 'admin.auth:banner-image-list@canModify']);
            });

        });

            Route::group(['prefix'=> 'settings','as' => 'settings-','middleware'=>'admin.auth:settings'],function(){
                Route::get('notifications',['as'=>'notifications','uses'=>'RoleManagement@getNotifications','middleware' => 'admin.auth:notifications@canView']);
                Route::get('role-management',['as'=>'role-management','uses'=>'RoleManagement@listRoleManagement','middleware' => 'admin.auth:role-management@canView']);
                Route::post('role-management',['as'=>'role-management','uses'=>'RoleManagement@postRoleManagement','middleware' => 'admin.auth:role-management@canAdd']);
                Route::get('role-permission-management',['as'=>'role-permission-management','uses'=>'RolePermissionManagement@listRolePermissionManagement','middleware' => 'admin.auth:role-permission-management@canView']);
                Route::post('role-permission-management',['as'=>'role-permission-management','uses'=>'RolePermissionManagement@postRolePermissionManagement','middleware' => 'admin.auth:role-permission-management@canModify']);

                Route::group(['as' => 'site-settings-','prefix' => 'site_setting'],function(){
                Route::get('list',['as'=>'list','uses'=>'SiteSettingCrud@show','middleware' => 'admin.auth:site-settings-list@canView']);
                Route::get('view/{id?}',['as'=>'view','uses'=>'SiteSettingCrud@view','middleware' => 'admin.auth:site-settings-list@canView']);
                Route::get('add',['as'=>'add','uses'=>'SiteSettingCrud@add','middleware' => 'admin.auth:site-settings-list@canAdd']);
                Route::post('insert',['as'=>'insert','uses'=>'SiteSettingCrud@insert','middleware' => 'admin.auth:site-settings-list@canAdd']);
                Route::get('edit/{id?}',['as'=>'edit','uses'=>'SiteSettingCrud@edit','middleware' => 'admin.auth:site-settings-list@canModify']);
                Route::post('update',['as'=>'update','uses'=>'SiteSettingCrud@update','middleware' => 'admin.auth:site-settings-list@canModify']);
                Route::get('delete/{id?}',['as'=>'delete','uses'=>'SiteSettingCrud@delete','middleware' => 'admin.auth:site-settings-list@canModify']);
                  });
      			  	Route::group(['as' => 'module-management-','prefix' => 'module'],function(){
      	  				Route::get('/',['uses'=>'ModuleCrud@show']);
      	  				Route::get('list',['as'=>'list','uses'=>'ModuleCrud@show']);
      	  				Route::get('view/{id?}',['as'=>'view','uses'=>'ModuleCrud@view']);
      	  				Route::get('add',['as'=>'add','uses'=>'ModuleCrud@add']);
      	  				Route::post('insert',['as'=>'insert','uses'=>'ModuleCrud@insert']);
      	  				Route::get('edit/{id?}',['as'=>'edit','uses'=>'ModuleCrud@edit']);
      	  				Route::post('update',['as'=>'update','uses'=>'ModuleCrud@update']);
      	  				Route::get('delete/{id?}',['as'=>'delete','uses'=>'ModuleCrud@delete']);
      	  			});
                Route::group(['as' => 'language-settings-','prefix' => 'language'],function(){
                Route::get('list',['as'=>'list','uses'=>'LanguageCrud@show','middleware' => 'admin.auth:language-settings-list@canView']);
                Route::get('view/{id?}',['as'=>'view','uses'=>'LanguageCrud@view','middleware' => 'admin.auth:language-settings-list@canView']);
                Route::get('add',['as'=>'add','uses'=>'LanguageCrud@add','middleware' => 'admin.auth:language-settings-list@canAdd']);
                Route::post('insert',['as'=>'insert','uses'=>'LanguageCrud@insert','middleware' => 'admin.auth:language-settings-list@canAdd']);
                Route::get('edit/{id?}',['as'=>'edit','uses'=>'LanguageCrud@edit','middleware' => 'admin.auth:language-settings-list@canModify']);
                Route::post('update',['as'=>'update','uses'=>'LanguageCrud@update','middleware' => 'admin.auth:language-settings-list@canModify']);
                Route::get('delete/{id?}',['as'=>'delete','uses'=>'LanguageCrud@delete','middleware' => 'admin.auth:language-settings-list@canModify']);
               });
               Route::group(['as' => 'currency-','prefix' => 'currency'],function(){
                Route::get('list',['as'=>'list','uses'=>'CurrencyCrud@show','middleware' => 'admin.auth:currency-list@canView']);
                Route::get('view/{id?}',['as'=>'view','uses'=>'CurrencyCrud@view','middleware' => 'admin.auth:currency-list@canView']);
                Route::get('add',['as'=>'add','uses'=>'CurrencyCrud@add','middleware' => 'admin.auth:currency-list@canAdd']);
                Route::post('insert',['as'=>'insert','uses'=>'CurrencyCrud@insert','middleware' => 'admin.auth:currency-list@canAdd']);
                Route::get('edit/{id?}',['as'=>'edit','uses'=>'CurrencyCrud@edit','middleware' => 'admin.auth:currency-list@canModify']);
                Route::post('update',['as'=>'update','uses'=>'CurrencyCrud@update','middleware' => 'admin.auth:currency-list@canModify']);
                Route::get('delete/{id?}',['as'=>'delete','uses'=>'CurrencyCrud@delete','middleware' => 'admin.auth:currency-list@canModify']);
               });
               Route::group(['as' => 'mail-template-','prefix' => 'mail_template'],function(){
                Route::get('list',['as'=>'list','uses'=>'MailTemplateCrud@show','middleware' => 'admin.auth:mail-template-list@canView']);
                Route::get('view/{id?}',['as'=>'view','uses'=>'MailTemplateCrud@view','middleware' => 'admin.auth:mail-template-list@canView']);
                Route::get('add',['as'=>'add','uses'=>'MailTemplateCrud@add','middleware' => 'admin.auth:mail-template-list@canAdd']);
                Route::post('insert',['as'=>'insert','uses'=>'MailTemplateCrud@insert','middleware' => 'admin.auth:mail-template-list@canAdd']);
                Route::get('edit/{id?}',['as'=>'edit','uses'=>'MailTemplateCrud@edit','middleware' => 'admin.auth:mail-template-list@canModify']);
                Route::post('update',['as'=>'update','uses'=>'MailTemplateCrud@update','middleware' => 'admin.auth:mail-template-list@canModify']);
                Route::get('delete/{id?}',['as'=>'delete','uses'=>'MailTemplateCrud@delete','middleware' => 'admin.auth:mail-template-list@canModify']);
               });
               Route::group(['as' => 'bet-rules-','prefix' => 'bet-rules'],function(){
                Route::get('list',['as'=>'list','uses'=>'BetRuleManagement@show','middleware' => 'admin.auth:bet-rules-list@canView']);
                Route::get('view/{id?}',['as'=>'view','uses'=>'BetRuleManagement@view','middleware' => 'admin.auth:bet-rules-list@canView']);
                Route::get('add',['as'=>'add','uses'=>'BetRuleManagement@add','middleware' => 'admin.auth:bet-rules-list@canAdd']);
                Route::post('insert',['as'=>'insert','uses'=>'BetRuleManagement@insert','middleware' => 'admin.auth:bet-rules-list@canAdd']);
                Route::get('edit/{id?}',['as'=>'edit','uses'=>'BetRuleManagement@edit','middleware' => 'admin.auth:bet-rules-list@canModify']);
                Route::post('update',['as'=>'update','uses'=>'BetRuleManagement@update','middleware' => 'admin.auth:bet-rules-list@canModify']);
                Route::get('delete/{id?}',['as'=>'delete','uses'=>'BetRuleManagement@delete','middleware' => 'admin.auth:bet-rules-list@canModify']);
               });
            });

        Route::group(['prefix'=> 'report-management','as' => 'report-management-','middleware'=>'admin.auth:report-management'],function()
        {
	         Route::get('deposit-report',['as'=>'deposit-report','uses'=>'Registration@getDepositReport','middleware' => 'admin.auth:deposit-report@canView']);
	         Route::get('withdrawal-report',['as'=>'withdrawal-report','uses'=>'Registration@getWithdrawalReport','middleware' => 'admin.auth:withdrawal-report@canView']);
           Route::get('single-bet-report',['as'=>'single-bet-report','uses'=>'ReportManagement@getSingleBetReport','middleware' => 'admin.auth:single-bet-report@canView']);
           Route::post('single-bet-report',['as'=>'single-bet-report','uses'=>'ReportManagement@postSingleBetReport','middleware' => 'admin.auth:single-bet-report@canView']);
           Route::post('single-bet-report-delete',['as'=>'single-bet-report-delete','uses'=>'ReportManagement@postSingleBetReportDelete','middleware' => 'admin.auth:single-bet-report@canModify']);
           Route::get('combo-bet-report',['as'=>'combo-bet-report','uses'=>'ReportManagement@getComboBetReport','middleware' => 'admin.auth:combo-bet-report@canView']);
           Route::post('combo-bet-report',['as'=>'combo-bet-report','uses'=>'ReportManagement@postComboBetReport','middleware' => 'admin.auth:combo-bet-report@canView']);
           Route::post('combo-bet-slip',['as'=>'combo-bet-slip','uses'=>'ReportManagement@postComboBetSlip','middleware' => 'admin.auth:combo-bet-report@canView']);
           Route::post('combo-bet-slip-details',['as'=>'combo-bet-slip-details','uses'=>'ReportManagement@postComboBetSlipDetails','middleware' => 'admin.auth:combo-bet-report@canView']);
           Route::post('combo-bet-report-delete',['as'=>'combo-bet-report-delete','uses'=>'ReportManagement@postComboBetReportDelete','middleware' => 'admin.auth:combo-bet-report@canModify']);
        });

        Route::group(['prefix'=> 'support-ticket-management','namespace'=>'SupportTicket','as' => 'support-ticket-management-','middleware'=>'admin.auth:support-ticket-management'],function(){
          Route::get('view-tickets',['as'=>'view-tickets','uses'=>'Registration@getViewTickets','middleware' => 'admin.auth:view-tickets@canView']);

            Route::group(['as' => 'ticket-department-','prefix' => 'ticket-department','middleware'=>'admin.auth:ticket-department-'],function(){
                Route::get('/',['uses'=>'StDepartmentCrud@show','middleware' => 'admin.auth:ticket-department-@canView']);
          			Route::get('list',['as'=>'list','uses'=>'StDepartmentCrud@show','middleware' => 'admin.auth:ticket-department-@canView']);
          			Route::get('view/{id?}',['as'=>'view','uses'=>'StDepartmentCrud@view','middleware' => 'admin.auth:ticket-department-@canView']);
          			Route::get('add',['as'=>'add','uses'=>'StDepartmentCrud@add','middleware' => 'admin.auth:ticket-department-@canAdd']);
          			Route::post('insert',['as'=>'insert','uses'=>'StDepartmentCrud@insert','middleware' => 'admin.auth:ticket-department-@canAdd']);
          			Route::get('edit/{id?}',['as'=>'edit','uses'=>'StDepartmentCrud@edit','middleware' => 'admin.auth:ticket-department-@canModify']);
          			Route::post('update',['as'=>'update','uses'=>'StDepartmentCrud@update','middleware' => 'admin.auth:ticket-department-@canModify']);
          			Route::get('delete/{id?}',['as'=>'delete','uses'=>'StDepartmentCrud@delete','middleware' => 'admin.auth:ticket-department-@canModify']);
          		});

            Route::group(['as' => 'ticket-type-','prefix' => 'ticket-type','middleware'=>'admin.auth:ticket-type-'],function(){
                  Route::get('/',['uses'=>'StTypeCrud@show','middleware' => 'admin.auth:ticket-type-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'StTypeCrud@show','middleware' => 'admin.auth:ticket-type-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'StTypeCrud@view','middleware' => 'admin.auth:ticket-type-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'StTypeCrud@add','middleware' => 'admin.auth:ticket-type-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'StTypeCrud@insert','middleware' => 'admin.auth:ticket-type-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'StTypeCrud@edit','middleware' => 'admin.auth:ticket-type-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'StTypeCrud@update','middleware' => 'admin.auth:ticket-type-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'StTypeCrud@delete','middleware' => 'admin.auth:ticket-type-@canModify']);
            		});

            Route::group(['as' => 'ticket-priority-','prefix' => 'ticket-priority','middleware'=>'admin.auth:ticket-priority-'],function(){
                  Route::get('/',['uses'=>'StPriorityCrud@show','middleware' => 'admin.auth:ticket-priority-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'StPriorityCrud@show','middleware' => 'admin.auth:ticket-priority-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'StPriorityCrud@view','middleware' => 'admin.auth:ticket-priority-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'StPriorityCrud@add','middleware' => 'admin.auth:ticket-priority-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'StPriorityCrud@insert','middleware' => 'admin.auth:ticket-priority-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'StPriorityCrud@edit','middleware' => 'admin.auth:ticket-priority-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'StPriorityCrud@update','middleware' => 'admin.auth:ticket-priority-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'StPriorityCrud@delete','middleware' => 'admin.auth:ticket-priority-@canModify']);
            		});

            Route::group(['as' => 'ticket-status-type-','prefix' => 'ticket-status-type','middleware'=>'admin.auth:ticket-status-type-'],function(){
                  Route::get('/',['uses'=>'StStatusTypeCrud@show','middleware' => 'admin.auth:ticket-status-type-@canView']);
                  Route::get('list',['as'=>'list','uses'=>'StStatusTypeCrud@show','middleware' => 'admin.auth:ticket-status-type-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'StStatusTypeCrud@view','middleware' => 'admin.auth:ticket-status-type-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'StStatusTypeCrud@add','middleware' => 'admin.auth:ticket-status-type-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'StStatusTypeCrud@insert','middleware' => 'admin.auth:ticket-status-type-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'StStatusTypeCrud@edit','middleware' => 'admin.auth:ticket-status-type-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'StStatusTypeCrud@update','middleware' => 'admin.auth:ticket-status-type-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'StStatusTypeCrud@delete','middleware' => 'admin.auth:ticket-status-type-@canModify']);
            		});

            Route::group(['as' => 'all-tickets-','prefix' => 'all-support-tickets','middleware'=>'admin.auth:all-tickets-'],function(){
                  Route::get('/',['uses'=>'SupportTicketCrud@show','middleware' => 'admin.auth:all-tickets-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'SupportTicketCrud@show','middleware' => 'admin.auth:all-tickets-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'SupportTicketCrud@view','middleware' => 'admin.auth:all-tickets-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'SupportTicketCrud@add','middleware' => 'admin.auth:all-tickets-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'SupportTicketCrud@insert','middleware' => 'admin.auth:all-tickets-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'SupportTicketCrud@edit','middleware' => 'admin.auth:all-tickets-@canModify']);
                  Route::get('edit-ticket/{id?}',['as'=>'edit-ticket','uses'=>'SupportTicketCrud@getEditTicket','middleware' => 'admin.auth:all-tickets-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'SupportTicketCrud@update','middleware' => 'admin.auth:all-tickets-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'SupportTicketCrud@delete','middleware' => 'admin.auth:all-tickets-@canModify']);
            		});
                Route::get('add-ticket',['as'=>'add-ticket','uses'=>'SupportTicketCrud@getAddTicket','middleware' => 'admin.auth:all-tickets-@canAdd']);
                Route::post('add-ticket',['as'=>'add-ticket','uses'=>'SupportTicketCrud@postAddTicket','middleware' => 'admin.auth:all-tickets-@canAdd']);
                Route::post('edit-ticket',['as'=>'edit-ticket','uses'=>'SupportTicketCrud@postEditTicket','middleware' => 'admin.auth:all-tickets-@canAdd']);
                Route::post('change-allocate-to',['as'=>'change-allocate-to','uses'=>'SupportTicketCrud@postChangeAllocateTo','middleware' => 'admin.auth:all-tickets-@canModify']);
                Route::post('change-status',['as'=>'change-status','uses'=>'SupportTicketCrud@postChangeStatus','middleware' => 'admin.auth:all-tickets-@canModify']);
                Route::post('show-message',['as'=>'show-message','uses'=>'SupportTicketCrud@postShowMessage','middleware' => 'admin.auth:all-tickets-@canView']);
                Route::post('ticket-reply',['as'=>'ticket-reply','uses'=>'SupportTicketCrud@postTicketReply','middleware' => 'admin.auth:all-tickets-@canAdd']);

                Route::group(['as' => 'my-tickets-','prefix' => 'my-tickets','middleware'=>'admin.auth:my-tickets-'],function(){
                  Route::get('/',['uses'=>'MyTicketCrud@show','middleware' => 'admin.auth:my-tickets-@canView']);
            			Route::get('list',['as'=>'list','uses'=>'MyTicketCrud@show','middleware' => 'admin.auth:my-tickets-@canView']);
            			Route::get('view/{id?}',['as'=>'view','uses'=>'MyTicketCrud@view','middleware' => 'admin.auth:my-tickets-@canView']);
            			Route::get('add',['as'=>'add','uses'=>'MyTicketCrud@add','middleware' => 'admin.auth:my-tickets-@canAdd']);
            			Route::post('insert',['as'=>'insert','uses'=>'MyTicketCrud@insert','middleware' => 'admin.auth:my-tickets-@canAdd']);
            			Route::get('edit/{id?}',['as'=>'edit','uses'=>'MyTicketCrud@edit','middleware' => 'admin.auth:my-tickets-@canModify']);
                  Route::get('edit-my-ticket/{id?}',['as'=>'edit-my-ticket','uses'=>'MyTicketCrud@getEditTicket','middleware' => 'admin.auth:my-tickets-@canModify']);
            			Route::post('update',['as'=>'update','uses'=>'MyTicketCrud@update','middleware' => 'admin.auth:my-tickets-@canModify']);
            			Route::get('delete/{id?}',['as'=>'delete','uses'=>'MyTicketCrud@delete','middleware' => 'admin.auth:my-tickets-@canModify']);
            		});
                Route::get('add-my-ticket',['as'=>'add-my-ticket','uses'=>'MyTicketCrud@getAddTicket','middleware' => 'admin.auth:my-tickets-@canAdd']);
                Route::post('add-my-ticket',['as'=>'add-my-ticket','uses'=>'MyTicketCrud@postAddTicket','middleware' => 'admin.auth:my-tickets-@canAdd']);
                Route::post('edit-my-ticket',['as'=>'edit-my-ticket','uses'=>'MyTicketCrud@postEditTicket','middleware' => 'admin.auth:my-tickets-@canAdd']);
                Route::post('change-allocate-to',['as'=>'change-allocate-to','uses'=>'MyTicketCrud@postChangeAllocateTo','middleware' => 'admin.auth:my-tickets-@canModify']);
                Route::post('change-status',['as'=>'change-status','uses'=>'MyTicketCrud@postChangeStatus','middleware' => 'admin.auth:my-tickets-@canModify']);
                Route::post('show-message',['as'=>'show-message','uses'=>'MyTicketCrud@postShowMessage','middleware' => 'admin.auth:my-tickets-@canView']);
                Route::post('ticket-reply',['as'=>'ticket-reply','uses'=>'MyTicketCrud@postTicketReply','middleware' => 'admin.auth:my-tickets-@canAdd']);

        });

        Route::post('apex-site-admin/get-bet-rules',['as'=>'get-bet-rules','uses'=>'Registration@getBetRules']);
	      Route::post('apex-site-admin/get-basic-agent',['as'=>'get-basic-agent','uses'=>'Registration@getBasicAgents']);
        Route::post('apex-site-admin/get-players',['as'=>'get-players','uses'=>'Registration@getPlayers']);
        Route::post('apex-site-admin/admin-get-users',['as'=>'get-users','uses'=>'Registration@getUsers']);
        Route::post('apex-site-admin/admin-get-agents',['as'=>'get-agents','uses'=>'Registration@getAgents']);
        Route::post('admin-notification-get',['as'=>'admin-notification-get','uses' =>'Registration@notificationGet']);
        Route::get('apex-site-admin/unread-tickets',['as'=>'unread-tickets','uses'=>'Registration@getUnreadSupportTtickets']);


	  Route::get('dashboard',['as'=>'dashboard','uses'=>'Authentication@index']);
      Route::get('admin-coming-soon',['as'=>'coming-soon','uses'=>'Authentication@getComingSoon']);
      Route::get('admin-profile-settings',['as'=>'profile-settings','uses'=>'Authentication@getProfileSettings']);
      Route::post('admin-profile-settings',['as'=>'profile-settings','uses'=>'Authentication@postProfileSettings']);
	  Route::post('admin-logout',['as'=>'logout','uses'=>'Authentication@postLogout']);

        /**
          * Developers test routing starts here
          */
            Route::get('/see-selected-table-data', ['as' => 'see-selected-table-data', 'uses' => 'TestControllers\TableDalaController@index',]);
            Route::post('/post-selected-table-data', ['as' => 'post-selected-table-data', 'uses' => 'TestControllers\TableDalaController@postFetchAllTableData',]);
            Route::get('/translate', ['as' => 'translate', 'uses' => 'TestControllers\TableDalaController@setTranslation',]);
        /**
          * Developers test routing ends here
          */
});

/**
 * ======================================================================================================
 * 					Feed routing start from here
 * ======================================================================================================
 */
	Route::get('fetch-country','Feed\XmlFeed@fetchCountries');
	Route::get('fetch-sport','Feed\XmlFeed@fetchSports');
	Route::get('fetch-bookmaker','Feed\XmlFeed@fetchBookmakers');
	Route::get('fetch-market','Feed\XmlFeed@fetchMarkets');
	Route::get('save-market-group','Feed\XmlFeed@saveMarketGroup');
	Route::get('test-xml','Feed\XmlFeed@fetchTeams');
	Route::get('test-match','Feed\XmlFeed@fetchMatches');
	Route::get('test-odds','Feed\XmlFeed@fetchOdds');

    Route::get('fetch-live-feed','Feed\LiveFeed@fetchLiveMatch');
/**
 * ======================================================================================================
 *                  Feed routing ends here
 * ======================================================================================================
 */

/**
 * ======================================================================================================
 * 					BetCalculations routing starts from here
 * ======================================================================================================
 */
Route::get('/football-single-bet-calculation', ['as' => 'cal-football', 'uses' => 'BetCalculations\FootballCalculation@footballSingleBetCalculate',]);
Route::get('/football-combo-bet-calculation', ['as' => 'cal-football', 'uses' => 'BetCalculations\FootballCalculation@footballComboBetCalculate',]);
Route::get('/handball-single-bet-calculation', ['as' => 'cal-handball',	'uses' => 'BetCalculations\HandballCalculation@handballSingleBetCalculate',]);
Route::get('/handball-combo-bet-calculation', ['as' => 'cal-handball', 'uses' => 'BetCalculations\HandballCalculation@handballComboBetCalculate',]);
Route::get('/basketball-single-bet-calculation', ['as' => 'cal-basketball',	'uses' => 'BetCalculations\BasketballCalculation@basketballSingleBetCalculate',]);
Route::get('/basketball-combo-bet-calculation', ['as' => 'cal-basketball',	'uses' => 'BetCalculations\BasketballCalculation@basketballComboBetCalculate',]);
Route::get('/icehockey-single-bet-calculation', ['as' => 'cal-icehockey', 'uses' => 'BetCalculations\IceHockeyCalculation@icehockeySingleBetCalculate',]);
Route::get('/icehockey-combo-bet-calculation', ['as' => 'cal-icehockey', 'uses' => 'BetCalculations\IceHockeyCalculation@icehockeyComboBetCalculate',]);
Route::get('/tennis-single-bet-calculation', ['as' => 'cal-tennis',	'uses' => 'BetCalculations\TennisCalculation@tennisSingleBetCalculate',]);
Route::get('/tennis-combo-bet-calculation', ['as' => 'cal-tennis',	'uses' => 'BetCalculations\TennisCalculation@tennisComboBetCalculate',]);
Route::get('/volleyball-single-bet-calculation', ['as' => 'cal-volleyball',	'uses' => 'BetCalculations\VolleyballCalculation@volleyballSingleBetCalculate',]);
Route::get('/volleyball-combo-bet-calculation', ['as' => 'cal-volleyball',	'uses' => 'BetCalculations\VolleyballCalculation@volleyballComboBetCalculate',]);
Route::get('/baseball-single-bet-calculation', ['as' => 'cal-baseball', 'uses' => 'BetCalculations\BaseballCalculation@baseballSingleBetCalculate',]);
Route::get('/baseball-combo-bet-calculation', ['as' => 'cal-baseball', 'uses' => 'BetCalculations\BaseballCalculation@baseballComboBetCalculate',]);
Route::get('/american-football-single-bet-calculation', ['as' => 'cal-american-football', 'uses' => 'BetCalculations\AmericanFootballCalculation@americanfootballSingleBetCalculate',]);
Route::get('/american-football-combo-bet-calculation', ['as' => 'cal-american-football', 'uses' => 'BetCalculations\AmericanFootballCalculation@americanfootballComboBetCalculate',]);
Route::get('/mix-combo-bet-calculation', ['as' => 'cal-mix-combo', 'uses' => 'BetCalculations\MixComboCalculation@index',]);
/**
 * ======================================================================================================
 *                  BetCalculations routing ends here
 * ======================================================================================================
 */
