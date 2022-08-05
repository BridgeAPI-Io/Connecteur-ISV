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
* @author 202 ecommerce <tech@202-ecommerce.com>
* @copyright Bridge
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
*}
<form id="states_form" method="POST" class="defaultForm form-horizontal mt-2"
    action="{$configuration.url_form_config|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="states_submit" value="1" />
    <div class="row justify-content-center">
        <div class="col-xl-12 pr-5 pl-5">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-11">{l s='Setting up your order statuses' mod='bridge'}</div>
                </div>
                <div class="form-wrapper justify-content-center pt-4">
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="pending_state">
                            {{l s='Status of pending wire transfers' mod='bridge'}|escape:'htmlall':'UTF-8'|replace:'--br--':'<br />'}
                        </label>
                        <div class="col-lg-4 align-item-center form-select">
                            <select name="pending_state" class="form-control custom-select">
                                {foreach from=$configuration.order_states item=orderstate}
                                <option 
                                    name="{$orderstate.name|escape:'htmlall':'UTF-8'}" 
                                    value="{$orderstate.id|escape:'htmlall':'UTF-8'}" {if $orderstate.id===$configuration.pending_state}selected="selected" {/if}>
                                    {$orderstate.name|escape:'htmlall':'UTF-8'} </option> {/foreach} </select> <small class="form-text">
                                    {l s='The order will be set to the selected status while waiting for the transfer to be processed by the issuing bank' mod='bridge'}
                                    </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="received_state">
                            {l s='Status of executed wire transfers' mod='bridge'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <select name="received_state" class="form-control custom-select">
                                {foreach from=$configuration.order_states item=orderstate}
                                    <option name="{$orderstate.name|escape:'htmlall':'UTF-8'}" 
                                        value="{$orderstate.id|escape:'htmlall':'UTF-8'}" 
                                        {if $orderstate.id===$configuration.received_state}selected="selected" {/if}>
                                        {$orderstate.name|escape:'htmlall':'UTF-8'} 
                                    </option>
                                {/foreach} 
                            </select>
                            <small class="form-text">
                                {l s='The order will be placed in this status upon execution of payment by the issuing bank' mod='bridge'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1">
                        </label>
                        <div class="col-lg-4" id="status_informations">
                            <div class="card d-flex flex-row">
                                <div class="col-md-2 d-flex align-items-center justify-content-center alert-info">
                                    <i class="material-icons mi-settings">info_outline</i>
                                </div>
                                <div class="col-lg-10 p-1 pl-2">
                                    <small>
                                        {l s='Assigning the payment states to different statuses will change the events started by Prestashop. Exemple : on payment accepted, Prestashop sends an email to your customer to confirm his order.' mod='bridge'}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-lg btn-primary" type="submit">
                            {l s='Save' mod='bridge'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
</form>