/**
 * Module: @news_tldr.summarize.js
 */


import AjaxRequest from "@typo3/core/ajax/ajax-request.js"
import Notification from "@typo3/backend/notification.js";

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

    new AjaxRequest(url)
      .post(payload).then(async function (response) {
      const data = await response.resolve();
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

    }, function (error) {
      Notification.error(TYPO3.lang['tldr.alert.error'], error.response.status + ' ' + error.response.statusText);
    });
  }
}

export default new Summarize();
