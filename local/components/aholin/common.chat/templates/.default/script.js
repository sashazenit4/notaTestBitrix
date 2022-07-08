function newMessage(text, userId, iblockId) {
    // console.log(text, userId)
    BX.ajax.runComponentAction("aholin:common.chat", "newMessage", {
        mode: 'class',
        data: {
            text: text,
            fromUserId: userId,
            iblockId: iblockId
        }
    }).then(function (response) {
        if (typeof response.data === "object")
        {
            node = document.querySelector(".card-body__sub")

            node.insertAdjacentHTML('beforeend', '<div class="message"><p class="text">' + response.data.text + '</p><div class="info"><span class="sender">'+ response.data.fromUserId +'</span>&nbsp<span class="date">'+ response.data.dateCreate +'</span></div></div>')

        } else console.log(response.data)
    });
}