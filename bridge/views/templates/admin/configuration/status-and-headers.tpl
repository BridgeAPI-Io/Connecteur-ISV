{**
 * Copyright Bridge
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @copyright Bridge
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0) 
 *}
<div class="bridgeApp mb-2 pt-2 {if $alert == false} mt-5{/if}">
  <div class="row">
    <div class="col-sm-12 pr-5 pl-5">
      <div class="row justify-content-center">
        <div class="col-sm-4">    
          <div class="card">
            <div class="card-block d-flex justify-content-center align-items-center" style="width:385px;height:291px;">    
              <img src="/modules/bridge/views/img/image-marketing.png" style="width:325px;" alt="Bridge Officiel"> 
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="card" id="bridge_status_zone">
            <div class="card-block justify-content-start align-items-start" style="width:385px;height:291px;">  
              <h3 class="mt-2 ml-2 row col-xl-12">
                {l s='Status' mod='bridge'}
              </h3>
              <h4>
                {foreach from=$specifications item=spec}
                  <div class="row ml-4 mb-1">
                    {if $spec.ok == true}
                        <i class="material-icons" style="color:green;">check</i>
                    {else}
                        <i class="material-icons" style="color:red;">close</i>
                    {/if}
                    <span class="pt-1 inline"
                    {if empty($spec.title) != true}
                      title="{foreach from=$spec.title item=bank}{$bank|escape:'htmlall':'UTF-8'}{/foreach}" 
                    {/if}
                    >{$spec.name|escape:'htmlall':'UTF-8'}{if $spec.info != ''} - {/if}{$spec.info|escape:'htmlall':'UTF-8'}</span>
                  </div>
                {/foreach}
                <div class="row ml-4">                  
                  <i class="material-icons" style="color:grey;">help</i>  
                  <span class="pt-1 inline">
                    {l s='URL for webhook' mod='bridge'} 
                  </span>
                </div>
                <div class="row ml-4 mb-1">
                  <span class="inline" style="font-size:14px;color:#00aff0;">
                    <a href="#" id="copy-clip-bridge" data-clipboard-copy="{$webhook_url|escape:'htmlall':'UTF-8'}" title="{$webhook_url|escape:'htmlall':'UTF-8'}">
                      <span class="truncate">{$webhook_url_text|escape:'htmlall':'UTF-8'}</span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 24 24">
                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                      </svg>
                    </a>
                  </span>
                </div>
              </h4>
            </div> {* card-block *}
          </div> {* card *}
        </div> {* col-sm4 *}
      </div> {* row-justif *}
    </div> {* sm-12 *}
  </div> {* row *}  
  <div class="row mt-2 bridge-infos">
    <div class="col-sm-12">
      <div class="row justify-content-center">      
        <div class="col-xl-12 pr-5 pl-5">   
          <div class="card">
            <div class="card-header">
              <img src="/modules/bridge/views/img/logo-payment.png" class="text-md-center" alt="Bridge Officiel"> 
              <span class="pl-2">
                {l s='Accept your first payments in 10 minutes' mod='bridge'}
              </span>
            </div>
            <div class="card-block row justify-content-left pt-2">
              <div class="row col-lg-12 pb-2 pt-1">
                <div class="row col-lg-12 pl-0 mt-2">                
                  <h3>{l s='Test Bridge' mod='bridge'}</h3>
                </div>
                <div class="row col-lg-12 pl-3">
                  <ul>
                    <li>
                      <a href="https://dashboard.bridgeapi.io/signup?utm_campaign=connector_prestashop" target="_blank">
                        {l s='Create a Bridge account' mod='bridge'}
                      </a>
                    </li>
                    <li>
                      {l s='Create a test application' mod='bridge'}
                    </li>
                    <li>
                      {l s='Enable test mode below' mod='bridge'}
                    </li>
                    <li>
                      {l s='Insert test client ID and client Secret below' mod='bridge'}
                    </li>
                    <li>
                      <a href="https://bridgeapi.zendesk.com/hc/en-150/articles/4428826451602-Guide-How-to-make-your-first-test-payment-"
                        target="_blank">
                        {l s='Test payments' mod='bridge'}
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="row col-lg-12 pl-0 mt-2">                
                  <h3>{l s='Go to production' mod='bridge'}</h3>
                </div>
                <div class="row col-lg-12 pl-3">
                  <ul>
                    <li>
                      <a href="https://meetings.hubspot.com/david-l2" target="_blank">
                        {l s='Schedule an appointment here' mod='bridge'}
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="row col-lg-12 pl-0 mt-2">                
                  <h3>{l s='Need help ?' mod='bridge'}</h3>
                </div>
                <div class="row col-lg-12 pl-3">
                  <ul>
                    <li>
                      {l s='As for the solution, if you want to know the coverage available ?' mod='bridge'} 
                      <a href="https://bridgeapi.zendesk.com/hc/fr-fr" target="_blank">
                        {l s='Visit our FAQs here' mod='bridge'}
                      </a>
                    </li>
                    <li>
                      {l s='Having a problem setting up the solution ?' mod='bridge'} 
                      <a href="https://bridgeapi.zendesk.com/hc/fr-fr" target="_blank">
                        {l s='Contact our technical team here' mod='bridge'}
                      </a>
                    </li>
                    <li>
                      {l s='Having a technical issue in production ?' mod='bridge'} 
                      <a href="mailto:support@bridgeapi.io" target="_blank">
                        {l s='Contact our support here' mod='bridge'}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div> {* card-block *}
          </div> {* card *}        
        </div> {* row-justif *}
      </div> {* col-xl-12 *}
    </div> {* sm-12 *}
  </div> {* row *}  
</div>