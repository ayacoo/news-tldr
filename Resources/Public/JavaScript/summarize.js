/**
 * Module: @news_tldr.summarize.js
 */


import AjaxRequest from "@typo3/core/ajax/ajax-request.js"
import Notification from "@typo3/backend/notification.js";
import nprogress from "nprogress";

class Summarize {
  constructor() {
    document.querySelectorAll('.t3js-ayacoo-tldr').forEach((item) => {
      item.addEventListener('click', (event) => {
        event.preventDefault();
        this.update(event);
      })
    })
  }

  update(event) {
    const url = TYPO3.settings.ajaxUrls.ayacoo_news_tldr_summarize;
    const newsId = event.currentTarget.dataset.newsid;
    const payload = {
      uid: newsId
    }

    nprogress.start();
    new AjaxRequest(url)
      .post(payload).then(async function (response) {
      const data = await response.resolve();
      if (data.success === true) {
        Notification.success(
            TYPO3.lang['tldr.alert.success'],
            TYPO3.lang['tldr.alert.success.text']
        );

        var inputBox = document.querySelector('[data-formengine-input-name="data[tx_news_domain_model_news][' + newsId + '][teaser]"]');
        var speed = 50;

        inputBox.value = '';

        var i = 0;
        var timer = setInterval(function () {
          if (i < data.text.length) {
            inputBox.value += data.text.charAt(i);
            i++;
          } else {
            clearInterval(timer);
          }
        }, speed);
        nprogress.done();
      } else {
        Notification.error(TYPO3.lang['tldr.alert.error'], data.text);
        nprogress.done();
      }

    }, function (error) {
      Notification.error(TYPO3.lang['tldr.alert.error'], error.response.status + ' ' + error.response.statusText);
      nprogress.done();
    });
  }
}

export default new Summarize();
